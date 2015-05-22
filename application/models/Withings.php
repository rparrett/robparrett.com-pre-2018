<?php

class WithingsModel
{
    private $db;
    private $api;

    public function __construct($db, $api)
    {
        $this->_db = $db;
        $this->_api = $api;
    }

    public function updateWeightsLocalFromRemote($userid)
    {
        // grab the timestamp of the last locally stored weight measurement

        $sql = "SELECT time FROM withings WHERE userid = :userid ORDER BY time DESC LIMIT 1";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);

        $result = $stmt->execute();

        $row = $result->fetchArray(SQLITE3_ASSOC);

        $time = 0;
        if (isset($row['time'])) {
            $time = $row['time'] + 1;
        }

        // grab any weight measurements recorded after that from remote and
        // store them locally

        $weights = $this->getWeightsRemote($userid, $time);

        $sql = "
			INSERT INTO withings (userid, meastype, category, time, value) 
			VALUES (:userid, :meastype, :category, :time, :value)
		";
        $stmt = $this->_db->prepare($sql);

        foreach ($weights as $weight) {
            $stmt->bindValue(':userid', $weight['userid'], SQLITE3_INTEGER);
            $stmt->bindValue(':meastype', $weight['meastype'], SQLITE3_INTEGER);
            $stmt->bindValue(':category', $weight['category'], SQLITE3_INTEGER);
            $stmt->bindValue(':time', $weight['time'], SQLITE3_INTEGER);
            $stmt->bindValue(':value', $weight['value'], SQLITE3_FLOAT);
            $r = $stmt->execute();
        }
    }

    public function getWeightsLocal($userid)
    {
        $sql = "SELECT * FROM withings WHERE userid = :userid ORDER BY time ASC";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);

        $result = $stmt->execute();

        $rows = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function getWeightsRemote($userid, $fromTimestamp)
    {
        $response = $this->_api->getMeas($userid, $fromTimestamp);

        $rows = array();

        if ($response->status !== 0) {
            return $rows;
        }

        foreach ($response->body->measuregrps as $group) {
            // The measuregroup has been captured by a device and is known to belong
            // to this user (and is not ambiguous)
            if ($group->attrib !== 0) {
                continue;
            }

            // The category field indicates for each measure group whether the measures
            // in the group are measurements or targets.
            if ($group->category !== 1) {
                continue;
            }

            foreach ($group->measures as $measure) {
                if ($measure->type != WithingsAPIClient::MEASTYPE_WEIGHT) {
                    continue;
                }

                $value = $measure->value;

                // A unit which represents the power of 10 that has to be multiplied
                // by value to find the actual data (integer).

                $value = $value * pow(10, $measure->unit);

                // kg -> lb

                $value = $value * 2.20462;

                $row = array(
                    'userid'   => $userid,
                    'time'     => $group->date,
                    'meastype' => WithingsAPIClient::MEASTYPE_WEIGHT,
                    'category' => WithingsAPIClient::CATEGORY_MEASUREMENT,
                    'value'    => $value
                );

                $rows[] = $row;
            }
        }

        usort($rows, function ($a, $b) {
            if ($a['time'] > $b['time']) {
                return 1;
            } elseif ($a['time'] < $b['time']) {
                return -1;
            }
            return 0;
        });

        return $rows;
    }

    public function formatWeightsForHighCharts($weights)
    {
        $rows = array();

        foreach ($weights as $weight) {
            $weight['time'] = $weight['time'] * 1000;

            $rows[] = array($weight['time'], $weight['value']);
        }

        // Insert a null measurement if the last measurement recorded
        // was not today. Makes it clear that data is not up to date
        // if that's the case.

        $now = time() * 1000;
        if (count($rows) > 0 && $rows[count($rows)-1][0] + 24 * 60 * 60 * 1000 < $now) {
            $rows[] = array($now, null);
        }

        return json_encode($rows);
    }
}
