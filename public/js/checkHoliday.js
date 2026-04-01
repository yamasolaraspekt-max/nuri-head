$(document).ready(function () {
    $.ajax({
        url: "/check_holiday", // Update with your route
        type: "GET",
        success: function (response) {
            console.log(response.message);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        },
    });
});
