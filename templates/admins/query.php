<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['user_type'] !== 'admin') {
    header("Location: /");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col bg-gray-100">
    <?= loadPartial('top-banner') ?>
    <?= loadPartial('navbar') ?>

    <main class="container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">SQL Query Executor</h2>


        <div class="mb-4">
            <textarea id="queryInput" class="w-full p-3 border rounded-lg font-mono text-sm resize-none" rows="5" placeholder="Write your SQL query here..."></textarea>
        </div>


        <div class="flex justify-end">
            <button id="runQuery" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Run Query</button>
        </div>


        <div id="queryResult" class="mt-6 overflow-x-auto"></div>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>

    <script>
        document.getElementById("runQuery").addEventListener("click", function() {
            let query = document.getElementById("queryInput").value.trim();
            let resultDiv = document.getElementById("queryResult");


            resultDiv.innerHTML = "";

            if (query === "") {
                resultDiv.innerHTML = '<p class="text-red-500 font-bold">Query cannot be empty!</p>';
                return;
            }

            fetch("/admin/query/run", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        query: query
                    })
                })
                .then(response => response.json())
                .then(data => {
                    resultDiv.innerHTML = "";

                    if (data.error) {
                        resultDiv.innerHTML = '<p class="text-red-500 font-bold text-center">Error: ' + data.error + '</p>';
                    } else if (data.data && data.data.length > 0) {

                        let table = `<div class="overflow-x-auto">
                                        <table class="table-auto min-w-full border border-gray-300 mt-4">
                                            <thead>
                                                <tr class="bg-gray-200">`;

                        let headers = Object.keys(data.data[0]);
                        headers.forEach(header => {
                            table += `<th class="px-4 py-2 border whitespace-nowrap">${header}</th>`;
                        });
                        table += `</tr></thead><tbody>`;

                        data.data.forEach(row => {
                            table += '<tr class="hover:bg-gray-100">';
                            headers.forEach(header => {
                                table += `<td class="px-4 py-2 border text-sm text-gray-700">${row[header]}</td>`;
                            });
                            table += '</tr>';
                        });

                        table += '</tbody></table></div>';
                        resultDiv.innerHTML = table;
                    } else {
                        resultDiv.innerHTML = '<p class="text-green-500 font-bold text-center">' + data.message + '</p>';
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = '<p class="text-red-500 font-bold text-center">An unexpected error occurred!</p>';
                });
        });
    </script>
</body>

</html>