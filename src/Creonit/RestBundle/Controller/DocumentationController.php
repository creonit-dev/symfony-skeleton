<?php

namespace Creonit\RestBundle\Controller;

use Creonit\RestBundle\Annotation\AbstractParameter;
use Creonit\RestBundle\Annotation\QueryParameter;
use Creonit\RestBundle\Annotation\RequestParameter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class DocumentationController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $annotationReader = $this->get('annotation_reader');

        $spec = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'API'
            ],
            'servers' => [
                ['url' => '/api']
            ],
            'tags' => [],
            'paths' => [],
            'components' => []
        ];


        foreach ($this->get('router')->getRouteCollection()->all() as $route) {
            if (!$route->hasDefault('_controller')) {
                continue;
            }

            if (0 !== strpos($route->getPath(), '/api')) {
                continue;
            }

            list($class, $name) = explode('::', $route->getDefault('_controller'));

            $reflectionMethod = new \ReflectionMethod($class, $name);

            $summary = '';
            $description = '';

            if($docComment = $reflectionMethod->getDocComment()){
                $docComment = trim(preg_replace('#^[ \t]*/?\*+ */?#m', '', $docComment));
                $docComment = explode('@', $docComment, 2);
                $docComment = preg_split('/[\n\r]+/', $docComment[0], 2);
                $summary = trim($docComment['0']);
                $description = isset($docComment['1']) ? trim($docComment['1']) : '';
            }
            $tags = [];
            $method = 'get';
            if (preg_match('/\\\\(\w+)Controller$/', $class, $tagMatch)) {
                $tags[] = strtolower($tagMatch['1']);
            }

            $parameters = [];
            $requiredRequestBody = [];
            $requestBody = [];

            $annotations = $annotationReader->getMethodAnnotations($reflectionMethod);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Method) {
                    $method = $annotation->getMethods();
                    $method = strtolower($method[0]);

                } else if ($annotation instanceof AbstractParameter) {
                    if ($annotation->in == 'request') {
                        $parameter = [
                            'type' => $annotation->type,
                            'format' => $annotation->format,
                            'description' => $annotation->description,
                        ];

                        if ($annotation->format) {
                            $parameter['format'] = $annotation->format;
                        }

                        $requestBody[$annotation->name] = $parameter;

                        if ($annotation->required) {
                            $requiredRequestBody[] = $annotation->name;
                        }

                    } else {
                        $parameter = [
                            'name' => $annotation->name,
                            'in' => $annotation->in,
                            'description' => $annotation->description,
                            'required' => $annotation->required,
                            'type' => $annotation->type,
                        ];

                        if ($annotation->format) {
                            $parameter['format'] = $annotation->format;
                        }

                        $parameters[] = $parameter;
                    }
                }


            }

            $path = substr($route->getPath(), 4);

            $operation = [
                'summary' => $summary,
                'description' => $description,
                'tags' => $tags,
                'parameters' => $parameters,
                'responses' => [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object'
                                ]
                            ]
                        ]
                    ],
                    'default' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => [
                                            'type' => 'object'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ];

            if ($requestBody) {
                $operation['requestBody'] = [
                    'content' => [
                        'application/x-www-form-urlencoded' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => $requiredRequestBody,
                                'properties' => $requestBody
                            ]
                        ]
                    ]
                ];
            }

            $spec['paths'][$path][$method] = $operation;
        }


        $sourceSpecPath = $this->getParameter('kernel.project_dir') . '/app/config/openapi.yml';
        if(file_exists($sourceSpecPath)){
            $sourceSpec = Yaml::parse(file_get_contents($sourceSpecPath));
            $spec = array_replace_recursive($spec, $sourceSpec);
        }

        return $this->render('CreonitRestBundle:Documentation:index.html.twig', ['spec' => $spec]);
    }

}