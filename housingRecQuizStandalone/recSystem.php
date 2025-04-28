<?php
/*
Plugin Name: Well-being Recommendation Survey
Description: A custom plugin to recommend housing options based on user survey input. Made for the Rent, Stress and Student Success GLI Capstone Project 2025.
Version: 1.5
Author: Aidan Rasmussen McGee
*/

// Register shortcode [wellbeing_recommendation_form]
add_shortcode('wellbeing_recommendation_form', 'render_wellbeing_recommendation_form');

/*
 * Renders the well-being recommendation survey form and handles results.
 * Displays results at the top when submitted and shows an introduction bubble.
 */
function render_wellbeing_recommendation_form() {
    ob_start();

    // If form submitted, process and display results first
    if (isset($_POST['submit_survey'])) {
        $input   = array_map('sanitize_text_field', $_POST);
        $csvPath = plugin_dir_path(__FILE__) . 'housingDataPlugin.csv';
        if (!file_exists($csvPath)) {
            echo "<p style='color:red;'>Error: CSV file not found.</p>";
        } else {
            $rows   = array_map('str_getcsv', file($csvPath));
            $header = array_map('trim', array_shift($rows));

            // Map internal keys to human-readable questions
            $labels = [
                'academicYear'            => 'What academic year are you currently in?',
                'age'                     => 'What is your age?',
                'gender'                  => 'How do you identify your gender?',
                'inpersonRemote'          => 'Are you primarily an in-person or remote student?',
                'credits'                 => 'How many credits are you taking this semester or plan to?',
                'fulltimeParttime'        => 'Are you enrolled as a full-time or part-time student?',
                'liveWithHowMany'         => 'If you had to live with others, how many people would you be willing to live with?',
                'monthlyRent'             => 'How much are you willing to pay each month in rent?',
                'universityCostsPerSemester' => 'What are your university-related costs per semester?'
            ];

            $matches = [];
            $total   = count($input);
            // Score each record by number of exact matches
            foreach ($rows as $rec) {
                $assoc   = array_combine($header, $rec);
                $score   = 0;
                $matched = [];
                foreach ($input as $k => $v) {
                    if (isset($assoc[$k]) && $assoc[$k] === $v) {
                        $score++;
                        $matched[] = $labels[$k] ?? $k;
                    }
                }
                $assoc['match_score']    = $score;
                $assoc['matched_fields'] = $matched;
                $matches[] = $assoc;
            }

            // Sort by match_score then wellbeingScore
            usort($matches, function($a, $b) {
                if ($b['match_score'] === $a['match_score']) {
                    return (float)$b['wellbeingScore'] <=> (float)$a['wellbeingScore'];
                }
                return $b['match_score'] <=> $a['match_score'];
            });

            // Output top 3 results
            echo "<div id='survey-results' style='margin-bottom:30px;'>";
            echo "<h3 style='text-align:center; margin-bottom:20px;'>Top 3 Housing Recommendations:</h3>";
            for ($i = 0; $i < min(3, count($matches)); $i++) {
                $m   = $matches[$i];
                $sim = round(($m['match_score'] / $total) * 100, 2);
                echo "<div class='survey-box visible'>";
                echo "<p><strong>Rank #" . ($i + 1) . "</strong></p>";
                echo "<p><strong>Well-being Score:</strong> " . esc_html($m['wellbeingScore']) . "</p>";
                echo "<p><strong>Housing Type:</strong> " . esc_html($m['residence']) . "</p>";
                echo "<p><strong>Similarity:</strong> {$sim}%</p>";
                echo "<p><strong>Matched Questions:</strong> " . esc_html(implode(', ', $m['matched_fields'])) . "</p>";
                echo "</div>";
            }
            echo "</div>";
        }
    }

    // Title for survey
    echo "<h2 style='text-align:center; margin-bottom:20px;'>What Housing is Right for You?</h2>";
    
    // Explanation bubble at top
    echo "<div class='survey-box' style='background:#e8f5e9; border-left:5px solid #28a745;'>";
    echo "<p>Welcome!";;
    echo "<br>";
    echo "<br>This survey will ask a few questions about your academic status, living preferences, budget and more. ";
    echo "After submission, you’ll see your top housing recommendations based on how closely survey responses in our dataset match yours, ";
    echo "including a Well-being Score and similarity percentage.";
    echo "<br>";
    echo "<br>The Well‑being Score is a metric that reflects how closely a student’s housing circumstances and personal factors align with those patterns in our data associated with higher overall mental and financial wellness. A higher score indicates that the recommended housing option is likely to support better stress management, social support, and overall quality of life. ";
    echo "The similarity percentage indicates how many of your survey responses match the recommended housing option. A higher percentage means that the recommended housing option is more closely aligned with your preferences and circumstances.<br>";
    echo "<br>The average student has a Well-Being Score of 62 out of 100. Recommendations with a higher than average score display healthier lifestyles and better well-being management.</p>";
    echo "</div>";

    // Render the survey form
    ?>
    <style>
    .survey-box { background:white; padding:20px; margin-bottom:30px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); opacity:0; transform:translateY(30px); transition:opacity 0.6s ease-out, transform 0.6s ease-out; }
    .survey-box.visible { opacity:1; transform:translateY(0); }
    .survey-question-label { font-weight:bold; display:block; margin-bottom:10px; }
    .survey-options { list-style:none; padding:0; margin:0; }
    .survey-options li { margin-bottom:8px; display:flex; align-items:center; cursor:pointer; }
    .survey-options input[type="radio"] { display:none; }
    .survey-options label { position:relative; padding-left:30px; }
    .survey-options label::before { content:''; position:absolute; left:0; top:2px; width:20px; height:20px; border:2px solid #555; border-radius:50%; background:white; }
    .survey-options input[type="radio"]:checked + label::after { content:''; position:absolute; left:6px; top:8px; width:8px; height:8px; border-radius:50%; background:#28a745; }
    </style>

    <form method="post">
        <?php
        $fields = [
            'academicYear'            => ["Freshman","Sophomore","Junior","Senior","Graduate Student","Other"],
            'age'                     => ["17 or younger","18","19","20","21","22","23","Older than 23 years"],
            'gender'                  => ["Man","Woman","Non-binary","Prefer to self-describe"],
            'inpersonRemote'          => ["In-person student (most classes on campus)","Remote student (all classes online)"],
            'credits'                 => ["1-6","7-12","13-18","More than 19"],
            'fulltimeParttime'        => ["Full-time student","Part-time student"],
            'liveWithHowMany'         => ["0","1","2","3","4","5","More than 5"],
            'monthlyRent'             => ["$250 or less","$251 - $500","$501 - $750","$751 - $1,000","$1,001 - $1,250","$1,251 - $1,500","$1,501 - $1,750","$1,751 - $2,000","More than $2,000","I do not pay rent"],
            'universityCostsPerSemester' => [
                "I do not have any University-related expenses (covered by outside sources)",
                "$1 - $1,000","$1,001 - $2,000","$2,001 - $3,000","$3,001 - $4,000","$4,001 - $5,000",
                "$5,001 - $6,000","$6,001 - $7,000","$7,001 - $8,000","$8,001 - $9,000","$9,001 - $10,000","More than $10,000"
            ]
        ];
        $labels = [
            'academicYear'            => 'What academic year are you currently in?',
            'age'                     => 'What is your age?',
            'gender'                  => 'How do you identify your gender?',
            'inpersonRemote'          => 'Are you primarily an in-person or remote student?',
            'credits'                 => 'How many credits are you taking this semester or plan to?',
            'fulltimeParttime'        => 'Are you enrolled as a full-time or part-time student?',
            'liveWithHowMany'         => 'If you had to live with others, how many people would you be willing to live with?',
            'monthlyRent'             => 'How much are you willing to pay each month in rent?',
            'universityCostsPerSemester' => 'What are your university-related costs per semester?'
        ];
        foreach ($fields as $name => $options) {
            echo "<div class='survey-box'>";
            echo "<span class='survey-question-label'>" . esc_html($labels[$name]) . "</span>";
            echo "<ul class='survey-options'>";
            foreach ($options as $idx => $opt) {
                $id = esc_attr("{$name}_{$idx}");
                echo "<li><input type='radio' name='".esc_attr($name)."' id='{$id}' value='".esc_attr($opt)."' required>";
                echo "<label for='{$id}'>".esc_html($opt)."</label></li>";
            }
            echo "</ul></div>";
        }
        ?>
        <input type="submit" name="submit_survey" value="Submit">
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', ()=>{
        const boxes=document.querySelectorAll('.survey-box');
        const obs=new IntersectionObserver((ents)=>{
            ents.forEach(e=>e.target.classList.toggle('visible', e.isIntersecting));
        },{threshold:0.15});
        boxes.forEach(b=>obs.observe(b));
    });
    </script>

    <?php
    return ob_get_clean();
}
?>