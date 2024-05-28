<x-filament-panels::page>


    @assets
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
          
            background-color: #fff;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }
        h2 {
            font-size: 1.75em;
            font-weight: bold;
            color: #444;
            margin-top: 39px;
        }
        h3 {
            font-size: 1.5em;
            font-weight: bold;
            color: #555;
            margin: 19px 0px;
        }
        h4 {
            font-size: 1.25em;
            font-weight: bold;
            color: #666;
            margin: 19px 0px;
        }
        h5 {
            font-size: 1em;
            font-weight: bold;
            color: #777;
            margin: 19px 0px;
        }
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            margin:20px 0px;
            color: red;
        }
        .table-container {
            overflow: auto;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
       
    </style>
    @endassets





    <div class="container">
      
        <h2>Endpoint</h2>
        <pre><strong>URL:</strong> <code>https://sweetbill.ng/api/v1/buy-data</code></pre>
        <p><strong>Method:</strong> POST</p>

        <h2>Headers</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Header</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>email</td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>User's email address</td>
                    </tr>
                    <tr>
                        <td>password</td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>User's password</td>
                    </tr>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>User's api_key. <a href="a-p-i-introduction" wire:navigate style="color:#fe5006;"> Get it from the Auth section!</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2>Request Body</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>data_id</td>
                        <td>Integer</td>
                        <td>Yes</td>
                        <td>ID of the mobile data plan</td>
                    </tr>
                    <tr>
                        <td>phone</td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>Recipient's phone number</td>
                    </tr>
                    <tr>
                        <td>requestId</td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>Unique transaction ID (format: YYYYMMDDHHmmss followed by alphanumeric or underscore)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2>Example Request</h2>
        <pre><code>curl -X POST https://sweetbill.ng/api/v1/buy-data \
     -H "email: user@example.com" \
     -d "data_id=1" \
     -d "phone=08012345678" \
     -d "requestId=2024052303325Z00KX23wE_DATA"</code></pre>

        <h2>Responses</h2>
        <h3>Success</h3>
        <p>If the request is successful, the endpoint returns a <code>200 OK</code> status with a success message.</p>
        <pre><code>{
    "status": "success",
    "message": "Data Transaction Successful!"
}</code></pre>

        <h3>Errors</h3>
        <p>If there is an error, the endpoint returns an appropriate error status with an error message.</p>

        <h4>Invalid Data ID (400 Bad Request)</h4>
        <pre><code>{
    "error": "Invalid Data ID"
}</code></pre>

        <h4>User Not Found (404 Not Found)</h4>
        <pre><code>{
    "error": "User not found"
}</code></pre>

        <h4>Duplicate Request ID (400 Bad Request)</h4>
        <pre><code>{
    "status": "failed",
    "error": "Duplicate requestId. Transaction already exists."
}</code></pre>

        <h4>Invalid requestId Format (400 Bad Request)</h4>
        <pre><code>{
    "error": "Invalid requestId format"
}</code></pre>

        <h4>Account Blocked (400 Bad Request)</h4>
        <pre><code>{
    "status": false,
    "error": "Your account has been blocked. Reach out to the SweetBill admin!"
}</code></pre>

        <h4>Insufficient Fund (400 Bad Request)</h4>
        <pre><code>{
    "status": "failed",
    "error": "Insufficient Fund!"
}</code></pre>

        <h4>Authentication Error (400 Bad Request)</h4>
        <pre><code>{
    "status": false,
    "error": "Authentication Error! Try again."
}</code></pre>

        <h4>General Error (500 Internal Server Error)</h4>
        <pre><code>{
    "status": "failed",
    "error": "Something went wrong. Please try again!"
}</code></pre>

        <h2>Response Codes</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Status Code</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>200</td>
                        <td>OK - Request succeeded</td>
                    </tr>
                    <tr>
                        <td>400</td>
                        <td>Bad Request - Invalid input or request</td>
                    </tr>
                    <tr>
                        <td>401</td>
                        <td>Unauthorized - Invalid credentials</td>
                    </tr>
                    <tr>
                        <td>404</td>
                        <td>Not Found - Resource not found</td>
                    </tr>
                    <tr>
                        <td>500</td>
                        <td>Internal Server Error - An error occurred on the server</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2>Notes</h2>
        <ul class="list-disc">
            <li>Ensure that you have a valid API key and correct user credentials before making a request.</li>
            <li>The <b>data_id</b> must be valid and active for the transaction to succeed. <a href="data-plans" wire:navigate style="color:#fe5006;">Check the available data plans here!</a></li>
        </ul>
    </div>


</x-filament-panels::page>
