<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

class NewsArticleSchema extends BaseArticle
{
    public function getType(): string
    {
        return 'NewsArticle';
    }
}
