"use strict";

// FOR IE 9 Fallback
window.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("form"); // Select DOM Form element
  form.addEventListener("submit", (ev) => { // Add submit listener

    let errors = new Array(); // Errors Array
    
    if(errors.length){        // If an error exists,
        ev.preventDefault(); // Prevent from submitting
    }
    // Form submitted
  });
});
