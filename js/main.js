function toggleFullScreen() {
  if (
    (document.fullScreenElement && document.fullScreenElement !== null) ||
    (!document.mozFullScreen && !document.webkitIsFullScreen)
  ) {
    if (document.documentElement.requestFullScreen) {
      document.documentElement.requestFullScreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullScreen) {
      document.documentElement.webkitRequestFullScreen(
        Element.ALLOW_KEYBOARD_INPUT
      );
    }
  } else {
    if (document.cancelFullScreen) {
      document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
      document.webkitCancelFullScreen();
    }
  }
}

//CODE FOR CUSTOM FORM//
$(document).ready(function () {
    // Add a click event handler to the #question_icon button
    $("#question_icon").click(function () {
      // Display the navigation help panel
      navigationPanel = document.getElementById("question_panel");
      // Check if the panel is currently open in the viewer
      if (navigationPanel.style.display === "flex") {
        // Hide the panel
        navigationPanel.style.display = "none";
      } else {
        // Make the panel visible
        navigationPanel.style.display = "flex";
      }
    });
  });
  