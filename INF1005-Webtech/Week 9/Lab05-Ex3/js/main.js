document.addEventListener("DOMContentLoaded", function () {
    registerEventListeners();
});

function registerEventListeners() {
    let thumbnails = document.getElementsByClassName("img-thumbnail");

    for (let i = 0; i < thumbnails.length; i++) {
        thumbnails[i].addEventListener("click", showPopup);
    }
}

function showPopup(event) {
    // Remove existing popup if present
    let existingPopup = document.querySelector(".img-popup");
    if (existingPopup) {
        existingPopup.remove();
    }

    // Create popup container
    let popup = document.createElement("span");
    popup.setAttribute("class", "img-popup");

    // Create large image
    let largeImgSrc = event.target.getAttribute("src");
    popup.innerHTML = `<img src="${largeImgSrc}" alt="">`;

    // Close popup when clicked
    popup.addEventListener("click", function () {
        popup.remove();
    });

    // Add popup to DOM
    document.body.insertAdjacentElement("beforeend", popup);
}

function activateMenu()
{
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link =>
    {
        if (link.href === location.href)
        {
        link.classList.add('active');
        }
    })
}
