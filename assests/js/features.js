document.addEventListener("DOMContentLoaded", function () {
  const toggles = document.querySelectorAll(".cmsmasters_toggle_wrap");

  toggles.forEach((toggle) => {
    const title = toggle.querySelector(".cmsmasters_toggle_title");
    title.addEventListener("click", () => {
      const content = toggle.querySelector(".cmsmasters_toggle");
      const isVisible = content.style.display === "block";
      document
        .querySelectorAll(".cmsmasters_toggle")
        .forEach((el) => (el.style.display = "none"));
      if (!isVisible) {
        content.style.display = "block";
      } else {
        content.style.display = "none";
      }
    });
  });
});
$("#myCollapse").on("shown.bs.collapse", function (event) {
  // Action to execute once the collapsible area is expanded
});
$("#myCarousel").on("slid.bs.carousel", function (event) {
  $("#myCarousel").carousel("2"); // Will slide to the slide 2 as soon as the transition to slide 1 is finished
});

$("#myCarousel").carousel("1"); // Will start sliding to the slide 1 and returns to the caller
$("#myCarousel").carousel("2"); // !! Will be ignored, as the transition to the slide 1 is not finished !!
// changes default for the modal plugin's `keyboard` option to false
$.fn.modal.Constructor.Default.keyboard = false;
