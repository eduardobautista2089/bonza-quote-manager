// document.addEventListener('DOMContentLoaded', function () {
//     // // Initial label change
//     // relabelButtons();

//     // Observe DOM mutations to reapply label changes
//     const metabox = document.querySelector('#submitdiv');
//     if (metabox) {
//         const observer = new MutationObserver(() => {
//             relabelButtons();
//         });

//         observer.observe(metabox, { childList: true, subtree: true });
//     }
// });

// function relabelButtons() {
//     // Change Publish/Update button to Approve
//     const publishButton = document.querySelector('#publish');
//     if (publishButton) {
//         publishButton.value = 'Approve';
//         publishButton.innerText = 'Approve';
//     }

//     // Change Save Draft to Reject
//     const saveDraft = document.querySelector('#save-post');
//     if (saveDraft) {
//         saveDraft.value = 'Reject';
//         saveDraft.innerText = 'Reject';
//     }

//     // Change "Draft" to "Reject" in dropdown
//     const statusOptions = document.querySelectorAll('#post_status option');
//     statusOptions.forEach(option => {
//         if (option.value === 'draft') {
//             option.textContent = 'Reject';
//         }
//     });

//     // Update visible post status display
//     const displayStatus = document.querySelector('#post-status-display');
//     if (displayStatus && displayStatus.textContent.trim() === 'Draft') {
//         displayStatus.textContent = 'Reject';
//     }
// }
