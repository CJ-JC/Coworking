function changeCarouselImage(workspaceId, imageSave) {
    var carouselId = "carouselExample" + workspaceId;
    var carousel = document.getElementById(carouselId);
    
    var carouselItems = carousel.querySelectorAll(".carousel-item");
    carouselItems.forEach(function(item) {
        item.classList.remove("active");
    });
    
    var newActiveItem = carousel.querySelector("img[src='/images/workspace/" + imageSave + "']").parentNode;
    newActiveItem.classList.add("active");
}