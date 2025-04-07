<?php
/*
Plugin Name: Well-being Recommendation Survey
Description: A custom plugin to recommend housing options based on user survey input.
Version: 1.2
Author: Your Name
*/

add_shortcode('wellbeing_recommendation_form', 'render_wellbeing_recommendation_form');

function render_wellbeing_recommendation_form() {
    ob_start();
    ?>

    <style>
    .survey-box {
        background: white;
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .survey-box.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .survey-question-label {
        font-weight: bold;
        display: block;
        margin-bottom: 10px;
    }

    .survey-options {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .survey-options li {
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .survey-options input[type="radio"] {
        display: none;
    }

    .survey-options label {
        position: relative;
        padding-left: 30px;
        cursor: pointer;
        user-select: none;
    }

    .survey-options label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 2px;
        width: 20px;
        height: 20px;
        border: 2px solid #555;
        border-radius: 50%;
        background-color: white;
    }

    .survey-options input[type="radio"]:checked + label::after {
        content: '';
        position: absolute;
        left: 6px;
        top: 8px;
        width: 8px;
        height: 8px;
        background-color: #28a745;
        border-radius: 50%;
    }
    </style>

    <form method="post">
        <?php
        $fields = [
            "universityCollege" => ["University of Montana", "Missoula College"],
            "academicYear" => ["Freshman", "Sophomore", "Junior", "Senior"],
            "age" => ["17 or younger", "18", "19", "20", "21", "22", "23", "Older than 23 years"],
            "gender" => ["Man", "Woman", "Non-binary", "Prefer To Self Describe below"],
            "inpersonRemote" => ["In-person student (most classes on campus)", "Remote student (all classes online)"],
            "credits" => ["1-6", "7-12", "13-18", "More than 19"],
            "fulltimeParttime" => ["Full-time student", "Part-time student"],
            "liveWithHowMany" => ["1", "2", "3", "4", "5", "More than 5"],
            "monthlyRent" => ["$250 or less", "$251 - $500", "$501 - $750", "$751 - $1,000", "$1,001 - $1,250", "$1,251 - $1,500", "$1,501 - $1,750", "$1,751 - $2,000", "More than $2,000", "I do not pay rent"],
            "universityCostsPerSemester" => [
                "I do not have any University-related expenses (my expenses are paid by outside sources, such as family support or scholarships)",
                "$1 - $1,000", "$1,001 - $2,000", "$2,001 - $3,000", "$3,001 - $4,000", "$4,001 - $5,000",
                "$5,001 - $6,000", "$6,001 - $7,000", "$7,001 - $8,000", "$8,001 - $9,000", "$9,001 - $10,000", "More than $10,000"
            ]
        ];

        foreach ($fields as $name => $options) {
            echo "<div class='survey-box'>";
            echo "<span class='survey-question-label'>" . ucfirst($name) . ":</span>";
            echo "<ul class='survey-options'>";
            foreach ($options as $index => $option) {
                $inputId = $name . '_' . $index;
                echo "<li>";
                echo "<input type='radio' name='" . esc_attr($name) . "' id='" . esc_attr($inputId) . "' value='" . esc_attr($option) . "' required>";
                echo "<label for='" . esc_attr($inputId) . "'>" . esc_html($option) . "</label>";
                echo "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        ?>
        <input type="submit" name="submit_survey" value="Submit">
    </form>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const boxes = document.querySelectorAll(".survey-box");
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                } else {
                    entry.target.classList.remove("visible");
                }
            });
        }, { threshold: 0.15 });

        boxes.forEach(box => observer.observe(box));
    });
    </script>

    <?php
    if (isset($_POST['submit_survey'])) {
        $input = array_map('sanitize_text_field', $_POST);
        $csv = plugin_dir_path(__FILE__) . 'surveyDataScoredLatest.csv';

        if (!file_exists($csv)) {
            echo "<p>Error: CSV file not found.</p>";
            return ob_get_clean();
        }

        $rows = array_map('str_getcsv', file($csv));
        $header = array_map('trim', $rows[0]);
        $data = array_slice($rows, 1);

        $matches = [];
        foreach ($data as $record) {
            $assoc = array_combine($header, $record);
            $score = 0;
            foreach ($input as $key => $value) {
                if (isset($assoc[$key]) && $assoc[$key] === $value) {
                    $score++;
                }
            }
            $assoc['match_score'] = $score;
            $matches[] = $assoc;
        }

        usort($matches, function ($a, $b) {
            if ($b['match_score'] == $a['match_score']) {
                return (float)$b['wellbeingScore'] <=> (float)$a['wellbeingScore'];
            }
            return $b['match_score'] <=> $a['match_score'];
        });

        echo "<h3>Top 3 Housing Recommendations:</h3>";
        for ($i = 0; $i < min(3, count($matches)); $i++) {
            $m = $matches[$i];
            echo "<div class='survey-box visible'>";
            echo "<p><strong>Rank #" . ($i+1) . "</strong></p>";
            echo "<p><strong>Well-being Score:</strong> " . esc_html($m['wellbeingScore']) . "</p>";
            echo "<p><strong>Recommended Housing Type:</strong> " . esc_html($m['residence']) . "</p>";
            echo "</div>";
        }
    }

    return ob_get_clean();
}
?>
