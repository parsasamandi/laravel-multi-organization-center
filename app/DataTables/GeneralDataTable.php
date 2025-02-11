<?php

namespace App\DataTables;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Crypt;
use Aws\S3\S3Client;
use Aws\CommandInterface;
use Aws\Exception\AwsException;

class GeneralDataTable
{
    /**
     * html builder | dataTable builder / table columns / table name
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html($builder, $columns, $table, $language = "persian.json")
    {
        return $builder
            ->setTableId("{$table}Table")
            ->minifiedAjax(route("{$table}.list.table", [], false))
            ->columns($columns)
            ->columnDefs(
                [
                    ["className" => 'dt-center text-center', "target" => '_all'],
                ]
            )
            ->searching(true)
            ->lengthMenu([10, 25, 40])
            ->info(false)
            ->ordering(true)
            ->responsive(true)
            ->pageLength(8)
            ->dom('frtip')
            ->orderBy(1)
            ->language(asset("js/" . $language));
    }

    // Computed column in datatables for delete, update, insertion
    public function setAction($id, $model = null)
    {
        $encryptedId = Crypt::encryptString($id); // Encrypt the ID
    
        $baseHtml = <<<HTML
            <a onclick="showConfirmationModal('{$encryptedId}')">
                <i class="fa fa-trash text-primary" aria-hidden="true"></i>
            </a>
            &nbsp;
            <a onclick="showEditModal('{$encryptedId}')">
                <i class="fa fa-edit text-primary" aria-hidden="true"></i>
            </a>
        HTML;
    
        if ($model) {
            $detailsUrl = url($model . '/details') . '?id=' . $encryptedId; // Use encrypted ID
            return $baseHtml . "&nbsp;" . "<a href='$detailsUrl'><i class='fa fa-info-circle text-primary' aria-hidden='true'></i></a>";
        }
    
        return $baseHtml;
    }
    


    /**
     * Get index column(0 | 1 | 2 .....).
     */
    public function getIndexCol()
    {
        return Column::make('DT_RowIndex')
                ->title('#')
                ->searchable(false)
                ->orderable(false);
    }

    /**
     * Get action column.
     */
    public function setActionCol()
    {
        return Column::computed('action') // This Column is not in database
                ->exportable(false)
                ->searchable(false)
                ->printable(false)
                ->orderable(false)
                ->title("#");
    }


    // Set status column
    public function setStatusCol($status) {

        if($status == Status::ACTIVE) return "موجود";
        else if($status == Status::INACTIVE) return 'ناموجود';
    }

    // Filter status column
    public function filterStatusCol($query, $rkeyword) {

        switch($keyword) {
            case 'موجود': $keyword = 0;
            break;
            case 'ناموجود': $keyword = 1;
        }

        return $this->filterColumn($query, 'id in (select status_id from
                        status where status like ?)', $keyword);
    }

    // Filter category column
    public function filterCategoryCol($query, $keyword) {

        return $this->filterColumn($query, 'category_id in
                (select id from categories where name like ?)', $keyword);
    }

    // Filter product column
    public function filterProductCol($query, $keyword) {

        return $this->filterColumn($query, 'product_id in
                (select id from product where name like ?)', $keyword);

    }

    // Filter column
    public function filterColumn($query, $sql, $keyword) {
        return $query->whereRaw($sql, ["%{$keyword}%"]);
    }

    // Download the url with its original name stored in the database with an hour time limit.
    public function getPresignedUrlWithContentDisposition($filePath, $fileName)
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $bucket = config('filesystems.disks.s3.bucket');
        
        // URL encode the filename to handle non-ASCII characters
        $encodedFileName = rawurlencode($fileName);

        try {
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key'    => $filePath,
                'ResponseContentDisposition' => 'attachment; filename="' . $encodedFileName . '"',
            ]);

            $request = $s3Client->createPresignedRequest($cmd, '+1 hour');
            return (string) $request->getUri();
        } catch (AwsException $e) {
            // Handle the error
            return null;
        }
    }

}
