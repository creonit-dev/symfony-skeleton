<?php

namespace AppBundle\Admin\Users;

use AppBundle\Admin\DownloadResultTrait;
use AppBundle\Model\User;
use AppBundle\Model\UserQuery;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\HttpFoundation\ParameterBag;

class UserTable extends TableComponent
{
    use DownloadResultTrait;


    /**
     * @title Список пользователей
     * @cols Имя, Группа, Электронная почта, Дата создания, .
     *
     * @header
     * <form class="form-inline pull-right">
     * {{ search | text({placeholder: 'Поиск', size: 'sm'}) }}
     * {{ submit('Обновить', {size: 'sm'}) }}
     * </form>
     *
     * {{ button('Добавить пользователя', {size: 'sm', type: 'success', icon: 'user'}) | open('UserEditor') }}
     * {{ button('Группы', {size: 'sm', icon: 'users'}) | open('GroupTable') }}
     * {{ button('Разрешения', {size: 'sm', icon: 'key'}) | open('RoleTable') }}
     *
     * {% if total %}
     *   &nbsp;&nbsp;
     *   {{ ('Найдено пользователей: ' ~ total) | icon('download') | action('downloadResult') | tooltip('Скачать список в CSV', 'right') }}
     * {% endif %}
     *
     * \User
     * @pagination 20
     *
     * @field group
     *
     * @col {{ name | icon('user') | open('UserEditor', {key: _key}) | controls }}
     * @col {{ (group ? group : '<b>Не назначена</b>' | raw) | open('UserGroupRelTable', {user: _key}) }}
     * @col {{ email }}
     * @col {{ created_at | date('d.m.Y') }}
     * @col {{ _delete() }}
     *
     */
    public function schema()
    {
        $this->addDownloadResultAction();
        $this->setHandler('download', [$this, 'download']);
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param ParameterBag $data
     * @param User $entity
     * @param Scope $scope
     * @param $relation
     * @param $relationValue
     * @param $level
     */
    protected function decorate(ComponentRequest $request, ComponentResponse $response, ParameterBag $data, $entity, Scope $scope, $relation, $relationValue, $level)
    {
        $data->set('group', $entity->getGroupTitles(', '));
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param UserQuery $query
     * @param Scope $scope
     * @param $relation
     * @param $relationValue
     * @param $level
     */
    protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
    {
        if ($search = $request->query->get('search')) {
            $query
                ->condition('c1', 'User.Name LIKE ?', "%{$search}%")
                ->condition('c2', 'User.Email LIKE ?', "%{$search}%")
                ->where(['c1', 'c2'], Criteria::LOGICAL_OR);
        }
    }

    protected function loadData(ComponentRequest $request, ComponentResponse $response)
    {
        parent::loadData($request, $response);

        $response->data->set('total', $response->data->get('pagination')[$this->getMask($this->getScope('User'), null, null)]['total']);
    }

    protected function download(ComponentRequest $request, ComponentResponse $response)
    {
        $this->downloadResult($request, $response, 'User',
            [
                'id',
                'name',
                'email',
                'created_at'
            ],
            function(User $user) {
                return [
                    $user->getId(),
                    $user->getName(),
                    $user->getEmail(),
                    $user->getCreatedAt('d.m.Y H:i:s'),
                ];
            }
        );
    }


}