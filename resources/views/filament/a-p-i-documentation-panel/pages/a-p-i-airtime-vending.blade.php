<x-filament-panels::page>


    @assets
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
          
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
            background: #f4f4f4;
            padding: 15px;
            color: red;
            border: 1px solid #ddd;
            overflow-x: auto;
        }
        .note {
            color: #777;
            font-size: 0.9em;
        }
        .responsive-table {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        code{
            color: red;
        }
    </style>
    @endassets


    <div class="container">
        <h1>Buy Airtime API Documentation</h1>
    
        <h2>Endpoint</h2>
        <pre><code><b>POST</b> &rarr; https://sweetbill.ng/api/v1/buy-airtime</code></pre>
    
        <h2>Headers</h2>
        <div class="responsive-table">
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
    
        <h2>Body Parameters</h2>
        <div class="responsive-table">
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>network</td>
                    <td>Integer</td>
                    <td>Yes</td>
                    <td>Network provider ID (1 for MTN, 2 for GLO, 3 for AIRTEL, 4 for 9MOBILE)</td>
                </tr>
                <tr>
                    <td>phone</td>
                    <td>String</td>
                    <td>Yes</td>
                    <td>Phone number to receive the airtime</td>
                </tr>
                <tr>
                    <td>bypass</td>
                    <td>Boolean</td>
                    <td>Yes</td>
                    <td>Bypass validation (true or false)</td>
                </tr>
                <tr>
                    <td>amount</td>
                    <td>Numeric</td>
                    <td>Yes</td>
                    <td>Amount of airtime to purchase</td>
                </tr>
                <tr>
                    <td>requestId</td>
                    <td>String</td>
                    <td>Yes</td>
                    <td>Unique request ID starting with YYYYMMDDHHmmss followed by alphanumeric characters or underscores</td>
                </tr>
            </table>
        </div>
    
        <h2>Responses</h2>
        
        <h3>Success Response</h3>
        <pre>
    {
        "status": "success",
        "message": "Airtime transaction successful!"
    }
        </pre>
        <br>
        <hr>
        <br>
        <h3>Error Response</h3>
        <h4>Invalid network ID (400 Bad Request)</h4>
        <pre>
    {
        "status": false,
        "error": "Invalid network ID"
    }
        </pre>
    
       
    
        <h4>Transaction Failed (400 Bad Request)</h4>
        <pre>
    {
        "status": "failed",
        "error": "Something went wrong. Please try again!"
    }
        </pre>
    
        <h2>Response Codes</h2>
        <div class="responsive-table">
            <table>
                <tr>
                    <th>Status Code</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td><code>200</code></td>
                    <td>OK - Request succeeded.</td>
                </tr>
                <tr>
                    <td><code>400</code></td>
                    <td>Bad Request - Invalid request or parameters.</td>
                </tr>
                <tr>
                    <td><code>401</code></td>
                    <td>Unauthorized - Invalid credentials.</td>
                </tr>
                <tr>
                    <td><code>404</code></td>
                    <td>Not Found - User not found.</td>
                </tr>
                <tr>
                    <td><code>500</code></td>
                    <td>Internal Server Error - An error occurred.</td>
                </tr>
            </table>
        </div>
    
        <h2>Notes</h2>
        <ul class="list-disc">
            <li>Ensure that you have a valid api_key and correct user credentials before making a request.</li>
            <li>The network field must be one of the following: <b>1</b> for <b>MTN</b>, <b>2</b> for <b>GLO</b>, <b>3</b> for <b>AIRTEL</b>, and <b>4</b> for <b>9MOBILE</b>.</li>
        </ul>
    
    </div>


</x-filament-panels::page>
