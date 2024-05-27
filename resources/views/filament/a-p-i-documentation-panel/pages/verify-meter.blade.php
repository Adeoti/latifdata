<x-filament-panels::page>


@assets
<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 20px;
    }
    h2 {
        color: #333;
        font-weight: bold;
        font-size: 29px;
        margin:12px 0px;
    }
    
    h3 {
        color: #333;
        font-weight: bold;
        font-size: 24px;
        margin:9px 0px;
    }
    
    h4 {
        color: #333;
        font-weight: bold;
        font-size: 22px;
        margin:8px 0px;
    }
    
    h5 {
        color: #333;
        font-weight: bold;
        font-size: 20px;
        margin:12px 0px;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
        margin: 20px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }
    table, th, td {
        border: 1px solid #ddd;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f4f4f4;
    }
    pre {
        background-color: #f6f8fa;
        border: 1px solid #ddd;
        border-radius: 3px;
        padding: 16px;
        overflow: auto;
    }
    code {
        font-size: 13px;
        color:red;
    }
    .error-table th {
        background-color: #ffdddd;
    }
    .error-table td {
        background-color: #ffecec;
    }
</style>
@endassets


<div style="background-color: #fff; border-radius:8px;" class="p-6">


    <h3>Endpoint</h3>
    <pre><code>POST https://sweetbill.ng/api/v1/verify-meter</code></pre>
    
    <h3>Description</h3>
    <p>This endpoint allows a registered user to verify an electricity meter number. The user must provide their email, password, and API key in the request headers. The meter number, meter type, and service ID must be included in the request body.</p>
    
    <h3>Headers</h3>
    <p>The following headers are required for authentication:</p>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>email</code></td>
                    <td>string (required)</td>
                    <td>The user's registered email.</td>
                </tr>
                <tr>
                    <td><code>password</code></td>
                    <td>string (required)</td>
                    <td>The user's password.</td>
                </tr>
                <tr>
                    <td><code>api_key</code></td>
                    <td>string (required)</td>
                    <td>The user's API key.</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <h3>Body Parameters</h3>
    <p>The following parameters must be included in the request body:</p>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>meter_number</code></td>
                    <td>string (required)</td>
                    <td>The meter number to verify.</td>
                </tr>
                <tr>
                    <td><code>meter_type</code></td>
                    <td>string (required)</td>
                    <td>Type of meter: <code>prepaid</code>, <code>postpaid</code>.</td>
                </tr>
                <tr>
                    <td><code>service_id</code></td>
                    <td>string (required)</td>
                    <td>The service ID for the meter verification.</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <h3>Request</h3>

    
    <h4>Example Request</h4>
    <pre><code>
    curl -X POST https://sweetbill.ng/api/v1/verify-meter \
         -H "email: user@example.com" \
         -H "password: user_password" \
         -H "api_key: user_api_key" \
         -d "meter_number=123456789" \
         -d "meter_type=prepaid" \
         -d "service_id=your_service_id"
    </code></pre>
    
    <h3>Response</h3>
    
    <h4>Success</h4>
    <p>If the credentials are valid and the meter number is successfully verified, the endpoint will return a <code>200 OK</code> status with the customer's name and address.</p>
    
    <h5>Response Body</h5>
    <pre><code>
    {
        "customer_name": "Ibrahim Adisa",
        "customer_address": "123 Main Street, Ibadan"
    }
    </code></pre>
    
    <h4>Errors</h4>
    <p>If there is an error, the endpoint will return an appropriate error message.</p>
    
    <h5>Invalid Meter Number</h5>
    <p>If the meter number is invalid:</p>
    <pre><code>
    {
        "error": "Invalid Meter Number. Kindly provide a valid Meter Number and try again!"
    }
    </code></pre>
    
    <h5>General Error</h5>
    <p>If there is a general error:</p>
    <pre><code>
    {
        "error": "Something went wrong. Please try again later or reach out to our reps for help."
    }
    </code></pre>    

</div>
</x-filament-panels::page>
