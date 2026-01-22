<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

class BlogPostingSchema extends BaseArticle
{
    public function getType(): string
    {
        return 'BlogPosting';
    }
}
