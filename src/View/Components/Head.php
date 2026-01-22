<?php

namespace BlackpigCreatif\Sceau\View\Components;

use BlackpigCreatif\Sceau\Enums\SchemaType;
use BlackpigCreatif\Sceau\Models\SeoData;
use BlackpigCreatif\Sceau\Models\SeoSettings;
use BlackpigCreatif\Sceau\Services\JsonLdGenerator;
use BlackpigCreatif\Sceau\Services\SchemaStack;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class Head extends Component
{
    public ?SeoData $seoData = null;

    public ?string $jsonLd = null;

    public function __construct(public ?Model $model = null)
    {
        $this->seoData = $this->resolveSeoData();
        $this->jsonLd = $this->generateJsonLd();
    }

    protected function resolveSeoData(): ?SeoData
    {
        if ($this->model === null) {
            return null;
        }

        if (method_exists($this->model, 'seoData')) {
            return $this->model->seoData;
        }

        return null;
    }

    protected function generateJsonLd(): ?string
    {
        $schemas = [];

        // 1. Add schemas from SeoData model (manual or auto-generated)
        if ($this->seoData) {
            $generator = app(JsonLdGenerator::class);

            // Generate schema from schema_type (respects manual schema_data if present)
            if ($schemaFromType = $generator->generateSchemaFromType($this->seoData)) {
                $schemas[] = $schemaFromType;
            }

            // Add FAQ schema if FAQ pairs exist in SeoData
            if ($this->seoData->hasFaqPairs()) {
                $faqGenerator = $generator->getGenerator(SchemaType::FAQPage);
                if ($faqGenerator) {
                    $schemas[] = $faqGenerator->generate($this->seoData);
                }
            }
        }

        // 2. Add runtime schemas from the stack (pushed by controllers/blocks)
        $runtimeSchemas = app(SchemaStack::class)->all();
        $schemas = array_merge($schemas, $runtimeSchemas);

        // 3. Add Organization schema from settings (if not already present)
        if ($this->shouldAddOrganizationSchema($schemas)) {
            if ($orgSchema = $this->getOrganizationSchema()) {
                $schemas[] = $orgSchema;
            }
        }

        if (empty($schemas)) {
            return null;
        }

        // Return single schema or array of schemas
        $output = count($schemas) === 1 ? $schemas[0] : $schemas;

        return json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Check if we should add an Organization schema.
     * Don't add if one already exists in the schemas array.
     */
    protected function shouldAddOrganizationSchema(array $schemas): bool
    {
        foreach ($schemas as $schema) {
            if (isset($schema['@type']) && in_array($schema['@type'], ['Organization', 'LocalBusiness'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get Organization schema from settings.
     */
    protected function getOrganizationSchema(): ?array
    {
        $settings = SeoSettings::first();

        if (! $settings?->site_name) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $settings->site_name,
        ];

        if ($settings->site_url) {
            $schema['url'] = $settings->site_url;
        }

        if ($settings->telephone) {
            $schema['telephone'] = $settings->telephone;
        }

        if ($settings->email) {
            $schema['email'] = $settings->email;
        }

        return $schema;
    }

    public function render(): View
    {
        return view('sceau::components.head', [
            'seoData' => $this->seoData,
            'jsonLd' => $this->jsonLd,
        ]);
    }
}
