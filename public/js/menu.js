function toggleProjectMenu() {
    document.getElementById("projectmenu").classList.toggle("show");
}

function toggleProjectSubMenu(submenuId) {
    document.getElementById(submenuId).classList.toggle("show");
}

// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
    let dropdown = document.getElementById("projectmenuDrop");
    let menu = document.getElementById("projectmenu");

    if (!dropdown.contains(event.target)) {
        menu.classList.remove("show");
    }
});



