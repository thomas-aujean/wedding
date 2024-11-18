import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');



(() => {
    const main = () => {
        let rsvpSelect = document.getElementById('attendingSelect');
        let attendingWrapper = document.getElementById('attendingWrapper');

        const handleRsvp = () => {
            if (rsvpSelect.value == 1) {
                displayFields()
            } else {
                hideFields()
            }
        }

        const hideFields = () => {
            attendingWrapper.classList.add("hide");
        }

        const displayFields = () => {
            attendingWrapper.classList.remove("hide");
        }


        if (rsvpSelect) {
            rsvpSelect.addEventListener('change', handleRsvp);
        }
    }


    window.addEventListener("load", main);
})();