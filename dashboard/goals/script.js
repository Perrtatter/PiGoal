async function updateGoalStep(slider) {
    const data = JSON.parse(slider.dataset.goal);
    const previousStep = slider.dataset.savedStep ?? slider.defaultValue;
    const step = Number(slider.value);

    try {
        const response = await fetch("goal_step_updater.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8" },
            body: new URLSearchParams({
                goal_id: String(data.goal_id),
                step: String(step)
            })
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
            slider.value = previousStep;
            return;
        }

        slider.dataset.savedStep = String(step);
        if (step === 3 && !data.is_complete) {
            send_post("goal_completer.php", data);
        } else if (step < 3 && data.is_complete) {
            send_post("goal_uncompleter.php", data);
        }
    } catch (error) {
        slider.value = previousStep;
        console.error("Could not save goal step:", error);
    }
}