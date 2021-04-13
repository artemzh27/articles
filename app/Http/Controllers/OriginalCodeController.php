<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOriginalCodeRequest;
use App\Services\ArticleService;
use App\Services\OriginalCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class OriginalCodeController extends Controller
{
    private ArticleService $articlesService;
    private OriginalCodeService $originalCodeService;

    /**
     * OriginalCodeController constructor.
     * @param ArticleService $articlesService
     * @param OriginalCodeService $originalCodeService
     */
    public function __construct(ArticleService $articlesService, OriginalCodeService $originalCodeService)
    {
        $this->articlesService = $articlesService;
        $this->originalCodeService = $originalCodeService;
    }

    /**
     * @param StoreOriginalCodeRequest $request
     * @param int $article_id
     * @return RedirectResponse|Redirector
     */
    public function store(StoreOriginalCodeRequest $request, int $article_id)
    {
        $article = $this->articlesService->getArticleById($article_id);
        $originalCode = $this->originalCodeService->getNewOriginalCode();
        $this->originalCodeService->updateOriginalCode($request, $article, $originalCode);

        session()->flash('success-message', 'Original code added.');

        return redirect(route('articles.edit', $article->id));
    }

    /**
     * @param int $articleId
     * @param int $id
     * @return RedirectResponse|Redirector
     */
    public function destroy(int $articleId, int $id)
    {
        $article = $this->articlesService->getArticleById($articleId);
        $originalCode = $this->originalCodeService->getOriginalCodeById($article, $id);

        try {
            $this->originalCodeService->deleteOriginalCode($originalCode);
            session()->flash('success-message', 'Original code deleted.');
        } catch (\Exception $e) {
            session()->flash('error-message', 'Cannot delete original code.');
        }

        return redirect(route('articles.edit', $articleId));
    }
}
