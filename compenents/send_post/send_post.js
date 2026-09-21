function send_post(php_link, data) {
    // 1. Create a virtual form in memory
    const virtualForm = Object.assign(document.createElement('form'), {
        action: php_link,
        method: 'POST'
    });

    // 2. Loop through all keys in your data object dynamically
    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            const input = Object.assign(document.createElement('input'), {
                type: 'hidden',
                name: key,          
                value: data[key]    
            });
            virtualForm.appendChild(input);
        }
    }

    // 3. Append to body and submit (the browser will redirect)
    document.body.appendChild(virtualForm);
    virtualForm.submit();
}