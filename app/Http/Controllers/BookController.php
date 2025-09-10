<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = auth()->user()->books()->paginate(10);
        return Inertia::render('PagesBooks/Index', 
        [
            'books' => $books
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'year' => 'required|string|max:255', 
                'month' => 'required|string|max:255',
                'dissemination_medium' => 'required|string|max:255',
                'start_page' => 'required|integer',
                'end_page' => 'required|integer',
                'total_pages' => 'required|integer',
                'book_series' => 'required|string|max:255',
                'edition' => 'required|string|max:255',
                'isbn' => 'required|string|max:255',
                'publication_place' => 'required|string|max:255',
                'publisher' => 'required|string|max:255',
                'publisher_type' => 'required|string|max:255',
                'discipline' => 'string|max:255',
                'evidence_document' => 'file|mimes:pdf,doc,docx,png,jpg',
                'credits_certificate' => 'file|mimes:pdf,doc,docx,png,jpg',
                'institution_endorsement_certificate' => 'file|mimes:pdf,doc,docx,png,jpg',
            ]);

            // Guardar archivos en storage/app/public/...
            if ($request->hasFile('evidence_document')) {
                $validated['evidence_document'] = $request->file('evidence_document')->store('evidences', 'public');
            }

            if ($request->hasFile('credits_certificate')) {
                $validated['credits_certificate'] = $request->file('credits_certificate')->store('credits', 'public');
            }

            if ($request->hasFile('institution_endorsement_certificate')) {
                $validated['institution_endorsement_certificate'] = $request->file('institution_endorsement_certificate')->store('endorsements', 'public');
            }

            // Crear registro en la misma tabla books
            $book = Book::create($validated);
            
            // Asociar al usuario logueado como autor
            $book->authors()->attach(auth()->id(), ['role' => 'Autor']);

            return response()->json([
                'success' => true,
                'message' => 'Libro creado correctamente.',
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear el libro.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'year' => 'required|string|max:255', 
                'month' => 'required|string|max:255',
                'dissemination_medium' => 'required|string|max:255',
                'start_page' => 'required|integer',
                'end_page' => 'required|integer',
                'total_pages' => 'required|integer',
                'book_series' => 'required|string|max:255',
                'edition' => 'required|string|max:255',
                'isbn' => 'required|string|max:255',
                'publication_place' => 'required|string|max:255',
                'publisher' => 'required|string|max:255',
                'publisher_type' => 'required|string|max:255',
                'discipline' => 'required|string|max:255',
                'evidence_document' => 'file|mimes:pdf,doc,docx,png,jpg',
                'credits_certificate' => 'file|mimes:pdf,doc,docx,png,jpg',
                'institution_endorsement_certificate' => 'file|mimes:pdf,doc,docx,png,jpg',
            ]);
            
            $book = Book::findOrFail($id);

            // Guardar archivos en storage/app/public/...
            if ($request->hasFile('evidence_document')) {
                $validated['evidence_document'] = $request->file('evidence_document')->store('evidences', 'public');
            }

            if ($request->hasFile('credits_certificate')) {
                $validated['credits_certificate'] = $request->file('credits_certificate')->store('credits', 'public');
            }

            if ($request->hasFile('institution_endorsement_certificate')) {
                $validated['institution_endorsement_certificate'] = $request->file('institution_endorsement_certificate')->store('endorsements', 'public');
            }

            // Crear registro en la misma tabla books
            $book->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Libro actualizado correctamente.',
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualir el libro.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();

            return response()->json(['message' => 'Libro eliminado correctamente']);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el libro.',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
