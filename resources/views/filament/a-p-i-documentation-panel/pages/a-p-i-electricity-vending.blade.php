<x-filament-panels::page>
@assets
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            
        }
        h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        h2 {
            font-size: 29px;
            font-weight: bold;
            margin: 12px 0;
            color: #333;
        }
        h3 {
            font-size: 24px;
            font-weight: bold;
            margin: 9px 0;
            color: #333;
        }
        h4 {
            font-size: 18px;
            font-weight: bold;
            margin: 8px 0;
            color: #333;
        }
        h5 {
            font-size: 15px;
            font-weight: bold;
            margin: 12px 0;
            color: #333;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
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
        code {
            color: red;
        }
    </style>
@endassets

<div class="container">
    <h1>Buy Electricity Subscription API Documentation</h1>

    <h2>Endpoint</h2>
    <pre><code><b>POST</b> &rarr; https://sweetbill.ng/api/v1/buy-electricity</code></pre>

    <h2>Description</h2>
    <p>This endpoint allows users to purchase electricity subscriptions on the Sweetbill platform. </p>

    <h2>Request Headers</h2>
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

    <h2>Request Body Parameters</h2>
    <div class="responsive-table">
        <table>
            <tr>
                <th>Parameter</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>meter_number</td>
                <td>Integer</td>
                <td>Yes</td>
                <td>The meter number for the electricity subscription.</td>
            </tr>
            <tr>
                <td>phone</td>
                <td>String</td>
                <td>Yes</td>
                <td>The phone number of the user.</td>
            </tr>
            <tr>
                <td>meter_type</td>
                <td>String</td>
                <td>Yes</td>
                <td>The type of the meter (e.g., "prepaid", "postpaid").</td>
            </tr>
            <tr>
                <td>disco_type</td>
                <td>String</td>
                <td>Yes</td>
                <td>The distribution company type (e.g., "ikeja-electric", "eko-electric").</td>
            </tr>      

            <tr>
                <td>amount</td>
                <td>Numeric</td>
                <td>Yes</td>
                <td>The amount to be paid for the electricity subscription.</td>
            </tr>
            <tr>
                <td>requestId</td>
                <td>String</td>
                <td>Yes</td>
                <td>A unique request identifier.</td>
            </tr>
        </table>
    </div>

    <h2>Available Disco Types</h2>
<div class="responsive-table">
    <table>
        <tr>
            <th>Disco Type</th>
            <th>Company Name</th>
        </tr>
        <tr>
            <td>ikeja-electric</td>
            <td>Ikeja Electricity</td>
        </tr>
        <tr>
            <td>eko-electric</td>
            <td>Eko Electricity</td>
        </tr>
        <tr>
            <td>kano-electric</td>
            <td>Kano Electricity</td>
        </tr>
        <tr>
            <td>portharcourt-electric</td>
            <td>Port Harcourt Electricity</td>
        </tr>
        <tr>
            <td>jos-electric</td>
            <td>Jos Electricity</td>
        </tr>
        <tr>
            <td>ibadan-electric</td>
            <td>Ibadan Electricity</td>
        </tr>
        <tr>
            <td>kaduna-electric</td>
            <td>Kaduna Electricity</td>
        </tr>
        <tr>
            <td>abuja-electric</td>
            <td>Abuja Electricity</td>
        </tr>
        <tr>
            <td>benin-electric</td>
            <td>Benin Electricity</td>
        </tr>
        <tr>
            <td>aba-electric</td>
            <td>ABA Electricity</td>
        </tr>
        <tr>
            <td>yola-electric</td>
            <td>YOLA Electricity</td>
        </tr>
    </table>
</div>

    <br>
    <hr>
    <br>
    <h2>Example Request</h2>
    <pre>
<code>
curl -X POST https://sweetbill.ng/api/v1/buy-electricity \
-H "Content-Type: application/json" \
-H "email: user@example.com" \
-d '{
"meter_number": 123456789,
"phone": "08012345678",
"meter_type": "prepaid",
"disco_type": "ikeja-electric",
"amount": 5000,
"requestId": "20240529153745TGqh2pjM_ELECTRICITY"
}'
</code>
    </pre>
 
    <h2>Example Response</h2>

    <h3>Success</h3>
    <pre>
<code>
{
"status": "success",
"message": "Electricity Transaction Successful!"
}
</code>
    </pre>

    <h3>Error (Insufficient Funds)</h3>
    <pre>
<code>
{
"status": "failed",
"error": "Insufficient Fund!"
}
</code>
    </pre>

    <h2>Notes</h2>
    <ul class="list-disc">
        <li>Ensure that the <code>requestId</code> is unique for each transaction to avoid duplicate processing.</li>
        <li>The amount specified in the <code>amount</code> parameter should be a numeric value representing the amount to be paid for the electricity subscription.</li>
        
    </ul>
</div>

</x-filament-panels::page>
