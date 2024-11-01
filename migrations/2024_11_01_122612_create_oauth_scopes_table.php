<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use function Hyperf\Config\config;

class CreateOauthScopesTable extends Migration
{
    protected $schema;

    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->schema = (new Schema())->connection($this->getConnection())->getSchemaBuilder();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->schema->create('oauth_scopes', function (Blueprint $table) {
            $table->string('id', 30)->primary();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $this->schema->dropIfExists('oauth_scopes');
    }

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection(): string
    {
        return config('oauth.provider');
    }
}
