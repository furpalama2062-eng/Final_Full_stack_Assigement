document.addEventListener('DOMContentLoaded', function() {
    const title = document.getElementById('title');
    const suggestionsDiv = document.getElementById('title-suggestions');

    function fetchAutocomplete() {
        const query = title.value.trim();
        if (!query) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        fetch('ajax_search.php?title=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                suggestionsDiv.innerHTML = '';
                if (data.length) {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item;
                        div.style.padding = '5px';
                        div.style.cursor = 'pointer';
                        div.addEventListener('click', () => {
                            title.value = item;
                            suggestionsDiv.style.display = 'none';
                        });
                        suggestionsDiv.appendChild(div);
                    });
                    suggestionsDiv.style.display = 'block';
                } else {
                    suggestionsDiv.style.display = 'none';
                }
            });
    }

    title.addEventListener('input', fetchAutocomplete);
    document.addEventListener('click', e => {
        if (e.target !== title) suggestionsDiv.style.display = 'none';
    });
});
