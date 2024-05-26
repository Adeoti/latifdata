<x-filament-panels::page>




<div class="bg-white p-6">



<h3 class="fi-header-heading text-lg font-bold tracking-tight text-gray-950 dark:text-white sm:text-lg">Endpoint</h3>


<pre class="p-1" style="background-color: #000000; overflow:auto; color:#ffffff; border-radius:7px; margin-top:7px;">
    GET &rarr; https://sweetbill.ng/api/v1/balance 
</pre>

<br><br>
<h3 class="fi-header-heading text-lg font-bold tracking-tight text-gray-950 dark:text-white sm:text-lg">Description</h3>
<br>

This endpoint allows a registered user to retrieve their account balance. To access this endpoint, the user must provide their email, password, and API key in the request headers.

<br><br>
<h3 class="fi-header-heading text-lg font-bold tracking-tight text-gray-950 dark:text-white sm:text-lg">Headers</h3><br>

The following headers are required for authentication:
<br>
<br>

    <ul class="list-disc">
        <li><b>email:</b> The user's registered email address.</li>
        <li><b>password:</b> The user's password.</li>
        <li><b>api_key:</b> The user's API key.</li>
    </ul>
   
<br><br>   
<h3 class="fi-header-heading text-lg font-bold tracking-tight text-gray-950 dark:text-white sm:text-lg"> Example Request </h3>

    <pre class="p-2" style="background-color: #000000; overflow:auto; color:#ffffff; border-radius:7px; margin-top:7px;">
        curl -H "email: user@example.com" \
     -H "password: user_password" \
     -H "api_key: user_api_key" \
     https://sweetbill.ng/api/v1/balance

    </pre>

    

<br>
<h3 class="fi-header-heading text-lg font-bold tracking-tight text-gray-950 dark:text-white sm:text-lg"> Response</h3><br>
<b>Success</b>

<p>If the credentials are valid, the endpoint will return a <b>`200 OK`</b> status with the user's balance.</p>

<pre class="p-2" style="background-color:  #000000; overflow:auto; color:#ffffff;">
    {<br>
        "balance": 100.00 <br>

    }
    
</pre>
<br>
<b>Error</b>

<p>If the credentials are invalid or missing, the endpoint will return a <b>`401 Unauthorized`</b> status with an error message.</p>

<pre class="p-2" style="background-color: #000000; overflow:auto; color:#ffffff;">
    {<br>
        "error": "Unauthorized" <br>

    }
    
</pre>


<br>
<table style="width: 100%; border-collapse: collapse; text-align: left;">
    <thead style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
        <tr>
            <th style="padding: 14px; border-bottom: 1px solid #e5e7eb;">Status Code</th>
            <th style="padding: 14px; border-bottom: 1px solid #e5e7eb;">Description</th>
        </tr>
    </thead>
    <tbody>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px;">200</td>
            <td style="padding: 12px;">OK - Request succeeded.</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px;">401</td>
            <td style="padding: 12px;">Unauthorized - Invalid credentials.</td>
        </tr>
    </tbody>
</table>

<hr>
<br> <br>
<div style="background-color: #fdfdfd;" class="p-2">
    
<b>Notes </b>

    <ul class="list-disc">
        <li>Ensure that you have a valid API key and correct user credentials before making a request.</li>
        <li>This endpoint is protected and requires proper authentication to access the user balance.</li>
    </ul>
</div>
    
    

    
</div>
    
    


</x-filament-panels::page>
