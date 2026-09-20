async function get_env(value) {
        const response = await fetch("/.env.json");

        // get json
        const json_payload = await response.json(); 

        // get value from json 
        return json_payload[value];
}


