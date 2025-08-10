// Get the modal
var modal = document.getElementById("myModal");

// Get the image and insert it inside the modal
var modalImg = document.getElementById("img01");

// Get all images in the gallery
var images = document.querySelectorAll(".gallery-item img");

images.forEach(function (image) {
  image.onclick = function () {
    modal.style.display = "flex";
    modalImg.src = this.src;
    modalImg.style.animation = "zoomIn 0.3s ease";
  };
});

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function () {
  modalImg.style.animation = "zoomOut 0.3s ease";
  setTimeout(function () {
    modal.style.display = "none";
  }, 300);
};
