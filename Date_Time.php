<?php
require_once 'Pdo_methods.php';

class Date_time extends Pdo_methods
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = new Pdo_methods();
    }

    /* Starting method — called from both index.php and display_notes.php */
    public function checkSubmit()
    {
        if (isset($_POST['addNote'])) {
            return $this->addNote();
        } elseif (isset($_POST['getNotes'])) {
            return $this->getNotes();
        }
        return '';
    }

    /* Handles adding a note from index.php */
    private function addNote()
    {
        $dateTime = isset($_POST['dateTime']) ? trim($_POST['dateTime']) : '';
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';

        // Validation — both fields required
        if ($dateTime === '' || $note === '') {
            return '<div class="alert alert-danger">You must enter a date, time, and note.</div>';
        }

        // Convert datetime-local value (e.g. "2024-11-25T09:16") to a Unix timestamp
        $timestamp = strtotime($dateTime);

        $sql = "INSERT INTO note (date_time, note) VALUES (:dateTime, :note)";
        $bindings = [
            [':dateTime', $timestamp, 'int'],
            [':note', $note, 'str'],
        ];

        $result = $this->pdo->otherBinded($sql, $bindings);

        if ($result === 'error') {
            return '<div class="alert alert-danger">An error occurred while saving the note. Please try again.</div>';
        }

        return '<div class="alert alert-success">Note added successfully.</div>';
    }

    /* Handles fetching notes for a date range from display_notes.php */
    private function getNotes()
    {
        $begDate = isset($_POST['begDate']) ? trim($_POST['begDate']) : '';
        $endDate = isset($_POST['endDate']) ? trim($_POST['endDate']) : '';

        // If either date is missing, treat as "no results"
        if ($begDate === '' || $endDate === '') {
            return '<div class="alert alert-warning">No notes found for the date range selected.</div>';
        }

        // Convert date strings to timestamps
        // begDate: start of the day (00:00:00)
        // endDate: end of the day (23:59:59)
        $begTimestamp = strtotime($begDate . ' 00:00:00');
        $endTimestamp = strtotime($endDate . ' 23:59:59');

        $sql = "SELECT date_time, note FROM note
                WHERE date_time BETWEEN :begDate AND :endDate
                ORDER BY date_time DESC";

        $bindings = [
            [':begDate', $begTimestamp, 'int'],
            [':endDate', $endTimestamp, 'int'],
        ];

        $rows = $this->pdo->selectBinded($sql, $bindings);

        if ($rows === 'error') {
            return '<div class="alert alert-danger">An error occurred while retrieving notes. Please try again.</div>';
        }

        if (empty($rows)) {
            return '<div class="alert alert-warning">No notes found for the date range selected.</div>';
        }

        // Build the results table
        $html = '<table class="table table-bordered table-striped mt-3">';
        $html .= '<thead><tr><th>Date and Time</th><th>Note</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($rows as $row) {
            // Convert timestamp back to mm/dd/yyyy HH:MM am/pm
            $formatted = date('n/d/Y h:i a', $row['date_time']);
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($formatted) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['note']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }
}
?>