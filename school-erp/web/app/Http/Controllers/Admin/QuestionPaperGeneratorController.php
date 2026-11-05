<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionPaperGeneratorController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::orderBy('id')->get();
        $subjects = Subject::orderBy('id')->get();

        return view('academics.question-paper-generator', compact('classes', 'subjects'));
    }

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required',
            'subject_id' => 'required',
            'exam_name' => 'required|string',
            'total_marks' => 'required|integer',
            'duration' => 'required|string',
            'chapter' => 'nullable|string',
        ]);

        $class = rescue(fn() => SchoolClass::find($validated['class_id'])?->name, 'Class 10');
        $subject = rescue(fn() => Subject::find($validated['subject_id'])?->name, 'Mathematics');
        $marks = (int)$validated['total_marks'];
        $examName = $validated['exam_name'];
        $duration = $validated['duration'];
        $chapter = $validated['chapter'] ?: 'Term 2 Syllabus Coverage';

        $paper = $this->buildQuestionPaper($class, $subject, $marks, $examName, $duration, $chapter);

        return response()->json([
            'status' => 'success',
            'paper' => $paper,
        ]);
    }

    private function buildQuestionPaper(string $class, string $subject, int $marks, string $examName, string $duration, string $chapter): array
    {
        if (str_contains(strtolower($subject), 'math')) {
            $partA = [
                ['q' => 'If the sum of first n terms of an AP is given by Sn = 3n^2 + 5n, find the common difference.', 'options' => ['(a) 3', '(b) 6', '(c) 5', '(d) 2'], 'ans' => '(b) 6'],
                ['q' => 'The roots of quadratic equation 2x^2 - 4x + 3 = 0 are:', 'options' => ['(a) Real & Equal', '(b) Real & Distinct', '(c) Not Real', '(d) Rational'], 'ans' => '(c) Not Real'],
                ['q' => 'The distance between points A(0, 6) and B(0, -2) is:', 'options' => ['(a) 6 units', '(b) 8 units', '(c) 4 units', '(d) 2 units'], 'ans' => '(b) 8 units'],
                ['q' => 'Find the value of sin^2(30°) + cos^2(30°):', 'options' => ['(a) 0', '(b) 1/2', '(c) 1', '(d) 2'], 'ans' => '(c) 1'],
            ];
            $partB = [
                ['q' => 'Prove that √5 is an irrational number.', 'ans' => 'Assume √5 = a/b (coprime). 5b^2 = a^2. Hence 5 divides a and b, contradicting coprimality.'],
                ['q' => 'Find the 20th term from the end of the AP: 3, 8, 13, ..., 253.', 'ans' => 'Reversing AP: a=253, d=-5. T20 = 253 + 19(-5) = 158.'],
                ['q' => 'Solve the linear system: 2x + 3y = 11 and 2x - 4y = -24.', 'ans' => 'Subtracting: 7y = 35 => y = 5. Substituting: x = -2.'],
            ];
            $partC = [
                ['q' => 'State and prove Basic Proportionality Theorem (Thales Theorem).', 'ans' => 'Statement: If a line is drawn parallel to one side of triangle to intersect other two sides in distinct points, other two sides are divided in same ratio. Draw diagram, prove via area ratio.'],
                ['q' => 'A motor boat whose speed is 18 km/h in still water takes 1 hour more to go 24 km upstream than to return downstream to the same spot. Find speed of stream.', 'ans' => 'Speed = s. Time diff: 24/(18-s) - 24/(18+s) = 1 => 48s = 324 - s^2 => s = 6 km/h.'],
            ];
        } else {
            $partA = [
                ['q' => 'The SI unit of electric resistance is:', 'options' => ['(a) Volt', '(b) Ampere', '(c) Ohm', '(d) Watt'], 'ans' => '(c) Ohm'],
                ['q' => 'Which of the following is a balanced displacement reaction?', 'options' => ['(a) Fe + CuSO4 -> FeSO4 + Cu', '(b) 2H2 + O2 -> 2H2O', '(c) CaCO3 -> CaO + CO2', '(d) None'], 'ans' => '(a) Fe + CuSO4 -> FeSO4 + Cu'],
                ['q' => 'The site of photosynthesis in plants is:', 'options' => ['(a) Mitochondria', '(b) Chloroplast', '(c) Ribosome', '(d) Golgi body'], 'ans' => '(b) Chloroplast'],
                ['q' => 'The chemical formula of Plaster of Paris is:', 'options' => ['(a) CaSO4.2H2O', '(b) CaSO4.1/2H2O', '(c) CaOCl2', '(d) Na2CO3'], 'ans' => '(b) CaSO4.1/2H2O'],
            ];
            $partB = [
                ['q' => 'State Ohm’s Law and write its mathematical formula.', 'ans' => 'Potential difference V across conductor is directly proportional to current I (V = I*R) at constant temperature.'],
                ['q' => 'Why does sky appear blue to a clear observer on Earth?', 'ans' => 'Rayleigh scattering: nitrogen/oxygen scatter shorter blue wavelengths (400nm) more effectively than red.'],
                ['q' => 'Differentiate between Autotrophic and Heterotrophic nutrition.', 'ans' => 'Autotrophs synthesize food via inorganic substances (photosynthesis). Heterotrophs depend on organic substances.'],
            ];
            $partC = [
                ['q' => 'Explain the working principle of an Electric Motor with neat labeled diagram.', 'ans' => 'Principle: Current-carrying conductor in magnetic field experiences torque (Flemings Left Hand Rule). Diagram: Armature, split rings, carbon brushes.'],
                ['q' => 'Describe the human digestive system with the role of enzymes in saliva, stomach, and small intestine.', 'ans' => 'Salivary amylase digests starch. Pepsin breaks protein in acidic medium. Pancreatic lipase and trypsin complete digestion in duodenum.'],
            ];
        }

        return [
            'school_name' => 'Test Subject Higher Secondary School, Pudukkottai',
            'board' => 'Tamil Nadu State Board &bull; Secondary Examination',
            'class' => "Class {$class}",
            'subject' => $subject,
            'exam_name' => $examName,
            'chapter' => $chapter,
            'duration' => $duration,
            'total_marks' => $marks,
            'part_a' => $partA,
            'part_b' => $partB,
            'part_c' => $partC,
        ];
    }
}
