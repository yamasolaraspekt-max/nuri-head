function toggleMenu(selectedMenu, imageId, activeImage, inactiveImage) {
    // Remove active state from all menus
    document.querySelectorAll(".menu").forEach((menu) => {
        menu.classList.remove("active_menu");
        let img = menu.querySelector("img");
        if (img) {
            let defaultImage = img.getAttribute("data-inactive"); // Retrieve the inactive state image
            img.src = defaultImage;
        }
    });

    // Add active state to clicked menu
    selectedMenu.classList.add("active_menu");
    let selectedImg = document.getElementById(imageId);
    if (selectedImg) {
        selectedImg.src = "{{ asset('images/dashboard/') }}" + activeImage; // Set active image
    }
}

// Store inactive images in data attributes for reference
document.querySelectorAll(".menu img").forEach((img) => {
    img.setAttribute("data-inactive", img.src);
});
