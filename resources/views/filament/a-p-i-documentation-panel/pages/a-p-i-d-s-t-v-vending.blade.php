<x-filament-panels::page>

@assets

<style>
    .api-docs-body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 20px;
        background-color: #f9f9f9;
    }
    .api-docs h1, .api-docs h2, .api-docs h3, .api-docs h4, .api-docs h5 {
        color: #333;
    }
    .api-docs h1 {
        font-size: 2.5em;
        font-weight: bold;
    }
    .api-docs h2 {
        font-size: 2em;
        font-weight: bold;
    }
    .api-docs h3 {
        font-size: 1.75em;
        font-weight: bold;
    }
    .api-docs h4 {
        font-size: 1.5em;
        font-weight: bold;
    }
    .api-docs h5 {
        font-size: 1.25em;
        font-weight: bold;
    }
    .api-docs .endpoint {
        background-color: #fff;
        padding: 10px;
        border-left: 5px solid #007bff;
        margin-bottom: 20px;
    }
    .api-docs .parameters, .api-docs .response, .api-docs .response-codes, .api-docs .api-docs-example {
        background-color: #fff;
        padding: 10px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
    .api-docs .parameters table, .api-docs .response table, .api-docs .response-codes table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .api-docs .parameters th, .api-docs .parameters td, .api-docs .response th, .api-docs .response td, .api-docs .response-codes th, .api-docs .response-codes td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .api-docs .parameters th, .api-docs .response th, .api-docs .response-codes th {
        background-color: #f2f2f2;
    }
    .api-docs .code {
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 10px;
        overflow-x: auto;
        white-space: pre-wrap;
    }
    .api-docs .code pre {
        margin: 0;
    }
</style>
@endassets



<div class="api-docs">
    <h1>Cable Subscription API Documentation</h1>
    <p>This documentation provides details about the cable (DSTV, GOTV, and STARTIMES) subscription endpoint, including request parameters and response formats.</p>

    <div class="endpoint">
        <h2>Endpoint</h2>
        <p><strong>URL:</strong> <code>https://sweetbill.ng/api/v1/buy-cable</code></p>
        <p><strong>Method:</strong> <code>POST</code></p>
    </div>

    <div class="parameters">
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

        <h2>Request Parameters</h2>
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
                    <td>cable_id</td>
                    <td>integer</td>
                    <td>Yes</td>
                    <td>ID of the cable subscription package. <a href="cable-plans" wire:navigate style="color:#fe5006;"> Get it from the Cable Plans section!</a></td>
                </tr>
                <tr>
                    <td>phone</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>Customer's phone number</td>
                </tr>
                <tr>
                    <td>decoder_number</td>
                    <td>numeric</td>
                    <td>Yes</td>
                    <td>Decoder number for the cable subscription</td>
                </tr>
                <tr>
                    <td>sub_type</td>
                    <td>string</td>
                    <td>No</td>
                    <td>Subscription type, either <code><b>change</b></code> or <code><b>renew</b></code> (required for DSVT and GOTV)</td>
                </tr>
                <tr>
                    <td>requestId</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>Unique request identifier, minimum 10 characters</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="api-docs-example">
        <h2>Example Request</h2>
        <pre><code>curl -X POST https://sweetbill.ng/api/v1/buy-cable \
    -H "email: user@example.com" \
    -H "password: user123#$$" \
    -H "api_key: 202405040704iuncrHgIai****" \
    -H "Content-Type: application/json" \
    -d '{ 
          "cable_id": 1, 
          "phone": "08012345678", 
          "decoder_number": 1234567890, 
          "sub_type": "renew", 
          "requestId": "20240529153745TGqh2pjM_CABLE" 
       }'</code></pre>



    </div>
<div class="response-codes">
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
</div>
    
    
</div>

</x-filament-panels::page>
