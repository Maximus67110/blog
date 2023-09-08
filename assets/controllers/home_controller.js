import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["list"];

    seeMore(e) {
        e.preventDefault()
        const url = this.listTarget.dataset.url;
        fetch(url)
            .then(response => response.text())
            .then(response => {
                this.listTarget.innerHTML += response;
            });
    }
}
