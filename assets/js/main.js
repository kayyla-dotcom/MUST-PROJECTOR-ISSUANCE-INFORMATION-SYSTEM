/*
================================================
  assets/js/main.js - Shared JavaScript
================================================

This file does two things:
  1. Ask "are you sure?" before deleting anything
  2. Auto-hide success/error messages after 4 seconds

This file is loaded by footer.php at the bottom
of every page, so it works everywhere automatically.

WHY AT THE BOTTOM?
  The script runs after the page has loaded.
  If it ran at the top, the HTML elements would
  not exist yet and querySelectorAll() would find nothing.
*/


/* ================================================
   1. CONFIRM BEFORE DELETE
   Any link with class="confirm-delete" will ask
   the user before following the link.

   Usage in PHP:
     <a href="delete.php?id=5" class="confirm-delete">Delete</a>
   ================================================ */

var deleteLinks = document.querySelectorAll('.confirm-delete');

for (var i = 0; i < deleteLinks.length; i++) {
    deleteLinks[i].addEventListener('click', function(e) {

        // confirm() shows a Yes/No popup and returns true or false
        var answer = confirm('Are you sure you want to delete this? This cannot be undone.');

        if (answer == false) {
            // User clicked Cancel - stop the link from working
            e.preventDefault();
        }
        // If answer is true, the link works normally
    });
}


/* ================================================
   2. AUTO-HIDE ALERT MESSAGES
   Any alert div with class="auto-hide" will fade
   out and disappear after 4 seconds.

   Usage in PHP:
     <div class="alert alert-success auto-hide">Saved!</div>

   NOTE: We use "alertBox" as the variable name
   (NOT "alert") because "alert" is a reserved word
   in JavaScript - it is the browser popup function.
   Using "alert" as a variable name breaks the code.
   ================================================ */

var alertBoxes = document.querySelectorAll('.auto-hide');

for (var j = 0; j < alertBoxes.length; j++) {

    // We wrap this in a function so each alertBox gets
    // its own copy of the variable (closure trick).
    // Without this, the loop would only hide the last one.
    (function(alertBox) {

        // Wait 4 seconds, then start fading out
        setTimeout(function() {

            // CSS transition makes it fade smoothly
            alertBox.style.transition = 'opacity 0.5s';
            alertBox.style.opacity    = '0';

            // After the fade (0.5s), hide it completely
            setTimeout(function() {
                alertBox.style.display = 'none';
            }, 500);

        }, 4000); // 4000ms = 4 seconds before fade starts

    })(alertBoxes[j]);
}
