document.addEventListener("DOMContentLoaded", function(event) {
    const div_list = document.querySelectorAll('.phone_number_for_barcode'); // returns NodeList
    const div_array = [...div_list]; // converts NodeList to Array
    
    div_array.forEach(div => {
        new QRCode(div, {
            text: div.textContent,
            width: 75,
            height: 75,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
})