<?php

namespace AppBundle\Admin;

use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;

trait DownloadResultTrait
{
    protected function addDownloadResultAction()
    {
        $this->setAction('downloadResult', "function(){
            var \$button = this.node.find('[js-component-action][data-name=\"downloadResult\"]');
            var \$icon = \$button.find('.icon');
            \$icon.addClass('fa-spin fa-spinner');
            this.request('download', this.getQuery(), null, function(response){
                \$icon.removeClass('fa-spin fa-spinner');
                if(this.checkResponse(response)){
                    document.location.href = response.data.url;
                }
            }.bind(this));
        }");
    }

    protected function downloadResult(ComponentRequest $request, ComponentResponse $response, $scopeName, $columns, Callable $decorate)
    {
        $scope = $this->getScope($scopeName);
        $query = $scope->createQuery();
        $this->filter($request, $response, $query, $scope, null, null, 0);

        $webDir = '/uploads/admin';
        $dir = $this->container->getParameter('kernel.project_dir') . '/web' . $webDir;
        $fileName = sprintf('%s-%s.csv', strtolower($scopeName), str_replace('.', '-', uniqid('', true)));
        $path = $dir . '/' . $fileName;
        $webPath = $webDir . '/' . $fileName;

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $resource = fopen($path, 'w');

        if ($columns) {
            fputcsv($resource, $columns);
        }

        foreach ($query->find() as $entity) {
            fputcsv($resource, $decorate($entity));
        }

        fclose($resource);

        $response->data->set('url', $webPath);
    }
}