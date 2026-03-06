<?php
include 'dbConnect.php';
// fetch data from database
// Get the disbursement status and approval status from the URL
$disbursement_status = isset($_GET['disbursement_status']) ? urldecode($_GET['disbursement_status']) : '';
$approval_status = isset($_GET['approval_status']) ? urldecode($_GET['approval_status']) : '';

// Build the SQL query based on the values of disbursement status and approval status
$sql = "SELECT loan_request.*, user.employee_no, user.names 
        FROM loan_request 
        JOIN user ON loan_request.user_id = user.id ";

if ($disbursement_status && $approval_status) {
  // Filter by both disbursement status and approval status
  $sql .= " WHERE loan_request.disbursement_status = '$disbursement_status' AND loan_request.approval_status = $approval_status";
} else if ($disbursement_status) {
  // Filter by disbursement status only
  $sql .= " WHERE loan_request.disbursement_status = '$disbursement_status'";
} else if ($approval_status) {
  // Filter by approval status only
  $sql .= " WHERE loan_request.approval_status = $approval_status";
} else {
  // No disbursement status or approval status specified, so export all data
  $sql .= " WHERE loan_request.disbursement_status != ''";
}
$data = mysqli_query($con, $sql);
if (!$data) {
    die('Error: ' . mysqli_error($con));
}


// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');


// send the headers to prompt the user to download the CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Loan Requests.csv"');

// write the headers to the CSV file
fputcsv($output, array('PayeeName','PayeeMobileNo', 'Amount', 'Reference'));

// write the data rows to the CSV file
while ($row = mysqli_fetch_assoc($data)) {
    fputcsv($output, array(
        $row['names'],
        '+' . $row['phone_number_to_disburse'],
        $row['amount_borrowed'],
        $row['type']
    ));
}

// close the file pointer
fclose($output);

// redirect back to the HTML file
// header('Location: requests.php');
exit();
?>
