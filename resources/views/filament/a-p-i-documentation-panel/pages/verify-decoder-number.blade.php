<x-filament-panels::page>

<div style="background-color: #ffffff; border-radius:8px;" class="p-6">
    


<h3>Endpoint</h3>
<p><code><b>POST</b> &rarr; https://sweetbill.ng/api/v1/verify-decoder</code></p>

<h3>Description</h3>
<p>This endpoint allows a registered user to verify a decoder number. The user must provide their email, password, and API key in the request headers. The decoder number and decoder type must also be included in the headers.</p>

<h3>Headers</h3>
<p>The following headers are required for authentication and verification:</p>
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
        <tr>
            <td><code>decoder_number</code></td>
            <td>string (required)</td>
            <td>The decoder number to verify.</td>
        </tr>
        <tr>
            <td><code>decoder_type</code></td>
            <td>string (required)</td>
            <td>Type of decoder: <code>dstv</code>, <code>gotv</code>, <code>startime</code>.</td>
        </tr>
    </tbody>
</table>
</div>

<h3>Request</h3>

<h4>URL</h4>
<p><code><b>POST</b> &rarr; https://sweetbill.ng/api/v1/verify-decoder</code></p>

<h4>Headers</h4>
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
        <tr>
            <td><code>decoder_number</code></td>
            <td>string (required)</td>
            <td>The decoder number to verify.</td>
        </tr>
        <tr>
            <td><code>decoder_type</code></td>
            <td>string (required)</td>
            <td>Type of decoder: <code>dstv</code>, <code>gotv</code>, <code>startime</code>.</td>
        </tr>
    </tbody>
</table>
</div>

<h4>Example Request</h4>
<pre><code>
curl -X POST https://sweetbill.ng/api/v1/verify-decoder \
     -H "email: user@example.com" \
     -H "password: user_password" \
     -H "api_key: user_api_key" \
     -H "decoder_number: 123456789" \
     -H "decoder_type: dstv"
</code></pre>

<h3>Response</h3>

<h4>Success</h4>
<p>If the credentials are valid and the decoder number is successfully verified, the endpoint will return a <code>200 OK</code> status with the customer's name and address.</p>

<h5>Response Body</h5>
<pre><code>
{
    "customer_name": "John Doe",
    "customer_address": "1234 Main St"
}
</code></pre>

<h4>Error</h4>
<p>If the credentials are invalid or the decoder number is incorrect, the endpoint will return an appropriate error status with an error message.</p>

<h5>Invalid Decoder Number (400 Bad Request)</h5>
<pre><code>
{
    "error": "Invalid Decoder Number. Kindly provide a valid Decoder Number and try again!"
}
</code></pre>

<h5>General Error (500 Internal Server Error)</h5>
<pre><code>
{
    "error": "Something went wrong. Please try again later or reach out to our reps for help."
}
</code></pre>

<h3>Error Codes</h3>

<div class="table-container">
<table class="error-table">
    <thead>
        <tr>
            <th>Status Code</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>200</code></td>
            <td>OK - Request succeeded.</td>
        </tr>
        <tr>
            <td><code>400</code></td>
            <td>Bad Request - Invalid decoder number provided.</td>
        </tr>
        <tr>
            <td><code>401</code></td>
            <td>Unauthorized - Invalid credentials.</td>
        </tr>
        <tr>
            <td><code>500</code></td>
            <td>Internal Server Error - An error occurred.</td>
        </tr>
    </tbody>
</table>
</div>

<h3>Notes</h3>
<ul>
    <li>Ensure that you have a valid API key and correct user credentials before making a request.</li>
    <li>The decoder type must be one of the following: <code>dstv</code>, <code>gotv</code>, or <code>startime</code>.</li>
</ul>


</div>

@assets

<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 20px;
    }
    h2 {
        color: #333;
    }
    h3{
        font-size: 20px;
        color:#000;
        font-weight: bold;
        margin:20px 0px;
    }
    h4{
        font-size: 17px;
        color:#000;
        font-weight: bold;
        margin:10px 0px;
    }
    h5{
        font-size: 15px;
        color:#000;
        font-weight: bold;
        margin:8px 0px;
    }
    h4{
        font-size: 17px;
        color:#000;
        font-weight: bold;
        margin:10px 0px;
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
        overflow: auto;
    }
    .error-table th {
        background-color: #ffdddd;
    }
    .error-table td {
        background-color: #ffecec;
    }
    .table-container {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
        }
</style>

@endassets


</x-filament-panels::page>
