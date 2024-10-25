document.addEventListener("DOMContentLoaded", function(event) {
    
    new QRCode(document.querySelector('#phone-barcode'), {
        text: document.querySelector('#phone-barcode').getAttribute("data-phone"),
        width: 75,
        height: 75,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    
})