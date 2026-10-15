<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

function respond($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $search = trim($_GET["search"] ?? "");
    $status = $_GET["status"] ?? "";
    $priority = $_GET["priority"] ?? "";

    $sql = "SELECT id, title, category, priority,
                   description, status, created_at
            FROM tickets
            WHERE title LIKE ?";

    $searchTerm = "%" . $search . "%";
    $types = "s";
    $params = [$searchTerm];

    if (in_array($status, ["Open", "In Progress", "Resolved"], true)) {
        $sql .= " AND status = ?";
        $types .= "s";
        $params[] = $status;
    }

    if (in_array($priority, ["Low", "Medium", "High"], true)) {
        $sql .= " AND priority = ?";
        $types .= "s";
        $params[] = $priority;
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();
    $tickets = [];

    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }

    $counts = [
        "total" => 0,
        "open" => 0,
        "progress" => 0,
        "resolved" => 0
    ];

    $countResult = $conn->query(
        "SELECT status, COUNT(*) AS total
         FROM tickets GROUP BY status"
    );

    while ($row = $countResult->fetch_assoc()) {
        $counts["total"] += (int)$row["total"];

        if ($row["status"] === "Open") {
            $counts["open"] = (int)$row["total"];
        } elseif ($row["status"] === "In Progress") {
            $counts["progress"] = (int)$row["total"];
        } elseif ($row["status"] === "Resolved") {
            $counts["resolved"] = (int)$row["total"];
        }
    }

    respond(200, [
        "success" => true,
        "tickets" => $tickets,
        "counts" => $counts
    ]);
}

if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        respond(400, ["success" => false, "message" => "Invalid request."]);
    }

    $action = $input["action"] ?? "";

    if ($action === "create") {
        $title = trim($input["title"] ?? "");
        $category = trim($input["category"] ?? "");
        $priority = $input["priority"] ?? "";
        $description = trim($input["description"] ?? "");

        $categories = ["Hardware", "Software", "Network", "Other"];
        $priorities = ["Low", "Medium", "High"];

        if (strlen($title) < 3 || strlen($title) > 150 ||
            strlen($description) < 5 || strlen($description) > 2000 ||
            !in_array($category, $categories, true) ||
            !in_array($priority, $priorities, true)) {
            respond(422, [
                "success" => false,
                "message" => "Check your title, category, priority and description."
            ]);
        }

        $stmt = $conn->prepare(
            "INSERT INTO tickets (title, category, priority, description)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("ssss", $title, $category, $priority, $description);

        if (!$stmt->execute()) {
            respond(500, ["success" => false, "message" => "Could not create ticket."]);
        }

        respond(201, [
            "success" => true,
            "message" => "Support ticket created successfully."
        ]);
    }

    if ($action === "update") {
        $id = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);
        $newStatus = $input["status"] ?? "";

        if (!$id || !in_array($newStatus, ["Open", "In Progress", "Resolved"], true)) {
            respond(422, ["success" => false, "message" => "Invalid ticket or status."]);
        }

        $stmt = $conn->prepare(
            "UPDATE tickets SET status = ? WHERE id = ?"
        );

        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $check = $conn->prepare("SELECT id, status FROM tickets WHERE id = ?");
            $check->bind_param("i", $id);
            $check->execute();

            if (!$check->get_result()->fetch_assoc()) {
                respond(404, ["success" => false, "message" => "Ticket not found."]);
            }
        }

        respond(200, ["success" => true, "message" => "Ticket status updated."]);
    }

    respond(400, ["success" => false, "message" => "Unknown action."]);
}

respond(405, ["success" => false, "message" => "Method not allowed."]);
?>