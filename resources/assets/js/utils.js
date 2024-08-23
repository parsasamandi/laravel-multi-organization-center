// Convert the English numbers of pagination to Persian
function convertNumbersToPersian(selector) {
    var persianNumbers = {
        '0': '۰',
        '1': '۱',
        '2': '۲',
        '3': '۳',
        '4': '۴',
        '5': '۵',
        '6': '۶',
        '7': '۷',
        '8': '۸',
        '9': '۹'
    };

    $(selector).each(function() {
        var text = $(this).text().trim();
        var newText = text.replace(/\d/g, function(digit) {
            return persianNumbers[digit];
        });
        $(this).text(newText);
    });
}