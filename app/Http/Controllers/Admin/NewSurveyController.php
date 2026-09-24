<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use DataTables;

class NewSurveyController extends Controller
{
    // Menampilkan Form Create Survey
    public function create()
    {
        return view('admin.survey.new.create');
    }

    // Menyimpan Survey beserta Pertanyaan & Opsinya
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Simpan Header Survey
            $surveyId = DB::connection('ifcaadm')->table('surveys')->insertGetId([
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Looping Pertanyaan
            if ($request->has('questions')) {
                foreach ($request->questions as $index => $q) {
                    $questionId = DB::connection('ifcaadm')->table('survey_questions')->insertGetId([
                        'survey_id' => $surveyId,
                        'question_text' => $q['text'],
                        'question_type' => $q['type'], // 'multiple_choice' atau 'essay'
                        'order_no' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // 3. Jika tipenya Multiple Choice, simpan opsi-opsinya
                    if ($q['type'] == 'multiple_choice' && isset($q['options'])) {
                        $optionsData = [];
                        foreach ($q['options'] as $opt) {
                            $optionsData[] = [
                                'question_id' => $questionId,
                                'option_text' => $opt,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        DB::connection('ifcaadm')->table('survey_question_options')->insert($optionsData);
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'OK', 'message' => 'Survey created successfully!']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'Failed', 'message' => 'Save failed: ' . $e->getMessage()]);
        }
    }

    // Fungsi untuk mensubmit jawaban responden

public function getDraftTable(Request $request)
    {
        // Ambil data survey yang statusnya 'draft'
        $query = DB::connection('ifcaadm')->table('surveys')
            ->select('id', 'title', 'description', 'status', 'created_at')
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn() 
            ->addColumn('row_number', function($row) {
                // Membuat nomor urut otomatis
                static $count = 0;
                return ++$count; 
            })
            ->make(true);
    }

    // 2. DataTables untuk Tab Published
    public function getPublishedTable(Request $request)
    {
        // Ambil data survey yang statusnya 'published'
        $query = DB::connection('ifcaadm')->table('surveys')
            ->select('id', 'title', 'publish_date', 'expired_date', 'status', 'created_at')
            ->where('status', 'published')
            ->orderBy('publish_date', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('row_number', function($row) {
                static $count = 0;
                return ++$count;
            })
            ->make(true);
    }

    // 3. Menghapus Survey (Berlaku untuk Draft dan Published)
    public function delete(Request $request)
    {
        try {
            // Karena di migrasi kita menggunakan onDelete('cascade'),
            // data pertanyaan (questions), opsi, dan jawaban otomatis terhapus saat header dihapus.
            DB::connection('ifcaadm')->table('surveys')->where('id', $request->id)->delete();

            return response()->json([
                'status' => 'OK',
                'message' => 'Data has been deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Fail',
                'message' => 'Delete failed: ' . $e->getMessage()
            ]);
        }
    }

    // 4. Menampilkan Form Set Tanggal Publish (di dalam Modal)
    public function publishForm($id)
    {
        $survey = DB::connection('ifcaadm')->table('surveys')->where('id', $id)->first();
        return view('admin.survey.new.publish_form', compact('survey'));
    }

    // 5. Menyimpan Status Publish beserta Tanggalnya
    public function publishSubmit(Request $request)
    {
        try {
            $publishDate = date('Y-m-d', strtotime($request->publish_date));
            $expiredDate = date('Y-m-d', strtotime($request->expired_date));

            DB::connection('ifcaadm')->table('surveys')->where('id', $request->survey_id)->update([
                'status' => 'published',
                'publish_date' => $publishDate,
                'expired_date' => $expiredDate,
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'OK',
                'message' => 'Survey published successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Fail',
                'message' => 'Publish failed: ' . $e->getMessage()
            ]);
        }
    }

    // Menampilkan Form Edit
    public function edit($id)
    {
        // Tambahkan connection('ifcaadm') di setiap pemanggilan DB
        $survey = DB::connection('ifcaadm')->table('surveys')->where('id', $id)->first();
        
        // Pastikan data survey ditemukan
        if (!$survey) {
            return "Survey data not found.";
        }

        $questions = DB::connection('ifcaadm')->table('survey_questions')
            ->where('survey_id', $id)
            ->orderBy('order_no', 'asc')
            ->get();

        foreach ($questions as $q) {
            if ($q->question_type == 'multiple_choice') {
                $q->options = DB::connection('ifcaadm')->table('survey_question_options')
                    ->where('question_id', $q->id)
                    ->get();
            } else {
                $q->options = [];
            }
        }

        return view('admin.survey.new.edit', compact('survey', 'questions'));
    }

    // Menyimpan Hasil Edit (Update)
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $surveyId = $request->survey_id;

            // 1. Update Header
            DB::connection('ifcaadm')->table('surveys')->where('id', $surveyId)->update([
                'title' => $request->title,
                'updated_at' => now(),
            ]);

            // 2. Hapus Pertanyaan & Opsi Lama (Hanya untuk Draft)
            DB::connection('ifcaadm')->table('survey_questions')->where('survey_id', $surveyId)->delete();

            // 3. Masukkan Pertanyaan yang Baru/Diedit
            if ($request->has('questions')) {
                foreach ($request->questions as $index => $q) {
                    $questionId = DB::connection('ifcaadm')->table('survey_questions')->insertGetId([
                        'survey_id' => $surveyId,
                        'question_text' => $q['text'],
                        'question_type' => $q['type'],
                        'order_no' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($q['type'] == 'multiple_choice' && isset($q['options'])) {
                        $optionsData = [];
                        foreach ($q['options'] as $opt) {
                            if (!empty($opt)) {
                                $optionsData[] = [
                                    'question_id' => $questionId,
                                    'option_text' => $opt,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                        if (count($optionsData) > 0) {
                            DB::connection('ifcaadm')->table('survey_question_options')->insert($optionsData);
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'OK', 'message' => 'Survey updated successfully!']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'Failed', 'message' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    // Menampilkan Laporan Semua Survey (Dengan Pagination 10 per halaman)
    public function allResults(Request $request)
    {
        // 1. Ambil 10 Survey per halaman (Hanya yang sudah published)
        $surveys = DB::connection('ifcaadm')->table('surveys')
            ->where('status', 'published')
            ->orderBy('publish_date', 'desc')
            ->paginate(10); // <-- Batas 10 ID per halaman

        // 2. Looping untuk mengambil data pertanyaan dan hitungan jawaban
        foreach ($surveys as $survey) {
            
            // Hitung total responden untuk survey ini
            $survey->totalRespondents = DB::connection('ifcaadm')->table('survey_respondents')
                ->where('survey_id', $survey->id)
                ->count();

            // Ambil pertanyaan
            $survey->questions = DB::connection('ifcaadm')->table('survey_questions')
                ->where('survey_id', $survey->id)
                ->orderBy('order_no', 'asc')
                ->get();

            foreach ($survey->questions as $q) {
                if ($q->question_type == 'multiple_choice') {
                    $q->options = DB::connection('ifcaadm')->table('survey_question_options')
                        ->where('question_id', $q->id)
                        ->get();

                    $totalAnswers = DB::connection('ifcaadm')->table('survey_answers')
                        ->where('question_id', $q->id)
                        ->count();

                    foreach ($q->options as $opt) {
                        $optCount = DB::connection('ifcaadm')->table('survey_answers')
                            ->where('option_id', $opt->id)
                            ->count();

                        $opt->count = $optCount;
                        $opt->percentage = $totalAnswers > 0 ? round(($optCount / $totalAnswers) * 100, 1) : 0;
                    }
                } else {
                    // Ambil SEMUA jawaban esai agar dipagination 10 data per halaman di frontend
                    $q->answers = DB::connection('ifcaadm')->table('survey_answers')
                        ->join('survey_respondents', 'survey_answers.respondent_id', '=', 'survey_respondents.id')
                        ->where('survey_answers.question_id', $q->id)
                        ->whereNotNull('survey_answers.essay_answer')
                        ->select('survey_answers.essay_answer', 'survey_respondents.email', 'survey_answers.created_at')
                        ->orderBy('survey_answers.created_at', 'desc')
                        ->get(); // <-- Gunakan ->get() tanpa ->take(10)
                }
            }
        }

        return view('admin.survey.new.result', compact('surveys'));
    }

    public function getOptionVoters(Request $request)
    {
        try {
            // 1. UBAH KONEKSI DI SINI JIKA PERLU (Ganti 'mysql' ke 'ifcaadm' jika itu yang benar)
            $voters = DB::connection('ifcaadm')->table('survey_answers') 
                ->join('survey_respondents', 'survey_answers.respondent_id', '=', 'survey_respondents.id')
                ->where('survey_answers.option_id', $request->option_id)
                ->select('survey_respondents.email', 'survey_answers.remarks', 'survey_answers.created_at')
                ->orderBy('survey_answers.created_at', 'desc')
                ->get();

            foreach ($voters as $v) {
                // 2. CEGAH ERROR JIKA created_at KOSONG/NULL
                $v->created_at = $v->created_at ? date('d M Y, H:i', strtotime($v->created_at)) : '-';
            }

            return response()->json([
                'status' => 'OK',
                'data'   => $voters
            ]);

        } catch (\Exception $e) {
            // 3. LEMPAR STATUS 500 AGAR JAVASCRIPT BISA MENANGKAP ERRORNYA
            return response()->json([
                'status'  => 'ERROR',
                'message' => 'DB Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
            ], 500); 
        }
    }
}