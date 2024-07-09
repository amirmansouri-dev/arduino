<?php
// app/Http/Controllers/ChatbotController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advice;
use App\Models\Query;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $question = $request->input('question');
        $temperature = $request->input('temperature');
        $humidity = $request->input('humidity');

        // Log the query
        $query = Query::create(['question' => $question]);

        // Extract keyword from the question
        $keyword = $this->extractKeyword($question);

        // If no keyword is found, return a specific message
        if (empty($keyword)) {
            return response()->json([
                'query' => $query->question,
                'response' => 'Sorry, I do not have advice for that topic.',
            ]);
        }

        // Find advice based on the keyword and conditions
        $adviceQuery = Advice::where(function($query) use ($keyword) {
            $query->where('title', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('content', 'LIKE', '%' . $keyword . '%');
        });

        $advice = $adviceQuery->first();

        if ($advice) {
            $response = $advice->content;
            $suitable = true;

            if ($temperature !== null) {
                if ($advice->temperature_min !== null && $advice->temperature_max !== null) {
                    if ($temperature < $advice->temperature_min || $temperature > $advice->temperature_max) {
                        $suitable = false;
                        $response .= " However, the current temperature of $temperature °C is not suitable. The suitable temperature range is " . $advice->temperature_min . "°C to " . $advice->temperature_max . "°C.";
                    } else {
                        $response .= " The current temperature of $temperature °C is suitable.";
                    }
                }
            }

            if ($humidity !== null) {
                if ($advice->humidity_min !== null && $advice->humidity_max !== null) {
                    if ($humidity < $advice->humidity_min || $humidity > $advice->humidity_max) {
                        $suitable = false;
                        $response .= " Additionally, the current humidity level of $humidity% is not suitable. The suitable humidity range is " . $advice->humidity_min . "% to " . $advice->humidity_max . "%.";
                    } else {
                        $response .= " The current humidity level of $humidity% is suitable.";
                    }
                }
            }

            if ($suitable) {
                return response()->json([
                    'query' => $query->question,
                    'response' => $response,
                ]);
            } else {
                return response()->json([
                    'query' => $query->question,
                    'response' => "Unfortunately, the current conditions are not suitable for this activity. " . $response,
                ]);
            }
        } else {
            return response()->json([
                'query' => $query->question,
                'response' => 'Sorry, I do not have advice for that topic.',
            ]);
        }
    }

    private function extractKeyword($question)
    {
        // Simple keyword extraction logic with Levenshtein distance
        $keywords = [

                    'tomates' => 'tomates',
                    'eau' => 'eau',
                    'sol' => 'sol',
                    'engrais' => 'engrais',
                    'plantation d\'arbres' => 'plantation d\'arbres',
                    'oliviers' => 'olivier',
                    'taille' => 'taille',
                    'fertilisation' => 'fertilisation',
                    'lumière' => 'lumière',
                    'température' => 'température',
                    'humidité' => 'humidité',
                    'arrosage' => 'arrosage',
                    'semis' => 'semis',
                    'pesticides' => 'pesticides',
                    'récolte' => 'récolte',
                    'maladies' => 'maladies',
                    'insectes' => 'insectes',
                    'serre' => 'serre',
                    'jardinage' => 'jardinage',
                    'culture' => 'culture',

        ];

        $question = strtolower($question);
        $closest = null;
        $shortest = -1;

        foreach ($keywords as $key => $value) {
            $lev = levenshtein($question, $key);
            if ($lev == 0) {
                $closest = $value;
                $shortest = 0;
                break;
            }

            if ($lev <= $shortest || $shortest < 0) {
                $closest = $value;
                $shortest = $lev;
            }
        }

        if ($shortest <= 3) { // Adjust the threshold as needed
            return $closest;
        }

        return '';
    }
}
