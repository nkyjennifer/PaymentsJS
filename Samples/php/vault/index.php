﻿@using PayJS_Samples.Misc
@{
    string MerchantId = "417227771521";
    string MerchantKey = "I5T2R2K6V1Q3";
    string PostbackUrl = "https://www.example.com/";

    string RequestId = "Invoice" + (new Random()).Next(100).ToString();
    string RequestType = "vault";
    string Amount = String.Empty;
    string Nonce = Guid.NewGuid().ToString();
    string CombinedString = RequestType + RequestId + MerchantId + PostbackUrl + Nonce + Amount;
    string AuthKey = Hmac.GetHmac(CombinedString, MerchantKey);

    string RequestId2 = "Invoice" + (new Random()).Next(100).ToString();
    string RequestType2 = "payment";
    string Amount2 = "1.00";
    string Nonce2 = Guid.NewGuid().ToString();
    string CombinedString2 = RequestType2 + RequestId2 + MerchantId + PostbackUrl + Nonce2 + Amount2;
    string AuthKey2 = Hmac.GetHmac(CombinedString2, MerchantKey);
}
<div class="wrapper text-center">
    <h1>Tokenization</h1>
    <p>Not ready to charge a card, or expecting to charge it multiple times? Run a vault-only request to store the card, then charge it when you're ready.</p>
    <br />
    <div>
        <button class="btn btn-primary" id="vaultButton">Store Card</button>
        <button class="btn btn-warning" id="paymentButton" disabled>Charge Card</button>
        <br /><br />
        <h5>Results:</h5>
        <p style="width:100%"><pre><code id="paymentResponse">The response will appear here as JSON, and in your browser console as a JavaScript object.</code></pre></p>
    </div>
</div>
<script type="text/javascript">
    PayJS(['PayJS/Request', 'PayJS/Response', 'PayJS/Core', 'PayJS/UI', 'jquery'],
    function($REQ, $RESP, $CORE, $UI, $) {
        $UI.Initialize({
            apiKey: "GvVtRUT9hIchmOO3j2ak4JgdGpIPYPG4",
            merchantId: "@MerchantId",
            authKey: "@AuthKey",
            requestType: "@RequestType",
            requestId: "@RequestId",
            amount: "@Amount",
            elementId: "vaultButton",
            debug: true,
            postbackUrl: "@PostbackUrl",
            phoneNumber: "1-800-555-1234",
            nonce: "@Nonce",
            //modalTitle: "Potatoes",
            //suppressResultPage: true
        });
        $UI.setCallback(function(vaultResponse) {
            console.log(vaultResponse.getResponse());
            $("#paymentResponse").text(
                vaultResponse.getResponse({ "json": true })
            );
            if (vaultResponse.getVaultSuccess()) {
                $("#vaultButton").prop('disabled', true);
                $("#paymentButton").prop('disabled', false);
                $("#paymentButton").click(function() {
                    $("#sps-holder").remove(); // remove initializations from previous samples
                    $CORE.Initialize({
                        apiKey: "GvVtRUT9hIchmOO3j2ak4JgdGpIPYPG4",
                        merchantId: "@MerchantId",
                        authKey: "@AuthKey2",
                        requestType: "@RequestType2",
                        requestId: "@RequestId2",
                        amount: "@Amount2",
                        applicationId: "DEMO",
                        debug: true,
                        postbackUrl: "@PostbackUrl",
                        nonce: "@Nonce2",
                    });
                    $REQ.doTokenPayment(vaultResponse.getVaultToken(), "123", function(paymentResponse) {
                        console.log(paymentResponse);
                    });
                })
            } else {
                // ...
            }
        });
    });
</script>

