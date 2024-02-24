<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Printable Report</title>
    <style>
        /* Define your print styles here */
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Report Data</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Expenses</th>
                <th>Range</th>
                <th>Receipt</th>
                <th>Description</th>
                <th>Type</th>
                <th>Jalali Month</th>
                <th>Jalali Year</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $report->expenses }}</td>
                <td>{{ $report->range }}</td>
                <td>{{ $report->receipt }}</td>
                <td>{{ $report->description }}</td>
                <td>{{ $report->type }}</td>
                <td>{{ $report->jalaliMonth }}</td>
                <td>{{ $report->jalaliYear }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
