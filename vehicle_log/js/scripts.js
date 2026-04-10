console.log("scripts are loaded!");

function toggle_active() {
    let inactive = document.getElementsByClassName("text-decoration-line-through");
    let toggle = document.getElementById("show_inactive");
    for (let i = 0; i < inactive.length; i++) {
        if (toggle.checked) {
            inactive[i].classList.remove("d-none");
        } else {
            inactive[i].classList.add("d-none");
        }
    }
    // Update result count
    let countEl = document.getElementById("resultCount");
    if (countEl) {
        let count = toggle.checked ? countEl.dataset.totalCount : countEl.dataset.activeCount;
        let label = countEl.dataset.label;
        countEl.textContent = count + ' result(s) for "' + label + '"';
    }
}
