<?php

require_once "../Core/Shared.php";

function getSum(PDO $pdo = null, int $days = 30): array {
    $isHourly = ($days === 1);
    $groupFormat = $isHourly ? '%H:00' : '%m-%d';
    $groupBy = $isHourly ? 'HOUR(so.paid_date)' : 'DATE(so.paid_date)';

    $select = "
        SELECT 
            ROUND(SUM(so.amount)) AS value, 
            DATE_FORMAT(so.paid_date, '{$groupFormat}') AS paidDate
        FROM servers_orders AS so 
        WHERE (so.status = 1 OR so.status = 2) 
          AND so.simulate = 0 
          AND so.paid_date >= NOW() - INTERVAL :days DAY
        GROUP BY {$groupBy}
        ORDER BY so.paid_date ASC
    ";

    $result = $pdo->prepare($select);
    $result->execute([
        ":days" => $days
    ]);

    $data = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            "value" => (int) $row["value"],
            "paidDate" => $row["paidDate"]
        ];
    }

    return $data;
}

function getAverage(PDO $pdo = null, int $days = 30): array {
    $isHourly = ($days === 1);
    $groupFormat = $isHourly ? '%H:00' : '%m-%d';
    $groupBy = $isHourly ? 'HOUR(so.paid_date)' : 'DATE(so.paid_date)';

    $select = "
        SELECT 
            ROUND(AVG(so.amount)) AS value, 
            DATE_FORMAT(so.paid_date, '{$groupFormat}') AS paidDate
        FROM servers_orders AS so 
        WHERE (so.status = 1 OR so.status = 2) 
          AND so.simulate = 0 
          AND so.paid_date >= NOW() - INTERVAL :days DAY
        GROUP BY {$groupBy}
        ORDER BY so.paid_date ASC
    ";

    $result = $pdo->prepare($select);
    $result->execute([
        ":days" => $days
    ]);

    $data = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            "value" => (int) $row["value"],
            "paidDate" => $row["paidDate"]
        ];
    }

    return $data;
}

function getMedian(PDO $pdo = null, int $days = 30): array {
    $isHourly = ($days === 1);
    $groupFormat = $isHourly ? '%H:00' : '%m-%d';
    $groupBy = $isHourly ? 'HOUR(sub.paid_date)' : 'DATE(sub.paid_date)';

    $select = "
        SELECT 
            DATE_FORMAT(sub.paid_date, '{$groupFormat}') AS paidDate,
            ROUND(SUBSTRING_INDEX(
                SUBSTRING_INDEX(
                    GROUP_CONCAT(sub.amount ORDER BY sub.amount),
                    ',', FLOOR(COUNT(*) / 2) + 1
                ),
                ',', -1
            )) AS value
        FROM (
            SELECT so.amount, so.paid_date
            FROM servers_orders AS so
            WHERE (so.status = 1 OR so.status = 2)
              AND so.simulate = 0
              AND so.paid_date >= NOW() - INTERVAL :days DAY
        ) AS sub
        GROUP BY {$groupBy}
        ORDER BY sub.paid_date ASC
    ";

    $result = $pdo->prepare($select);
    $result->execute([
        ":days" => $days
    ]);

    $data = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            "value" => (int) $row["value"],
            "paidDate" => $row["paidDate"]
        ];
    }

    return $data;
}

Shared::main(function(): void {
    if (!Shared::isPostComplete(["days"])) {
        Shared::invalidWay();
    }

    $pdo = Shared::getFDPdo();
    $days = (int) $_POST["days"];

    Shared::response(data: [
        "average" => getAverage($pdo, $days),
        "median" => getMedian($pdo, $days),
        "sum" => getSum($pdo, $days),
    ]);
});
