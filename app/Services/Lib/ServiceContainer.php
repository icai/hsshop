<?php 

namespace App\Services\Lib;

/**
 * 服务层容器
 */
class ServiceContainer
{
    protected $binds;

    protected $instances;

    /**
     * 注册一个绑定的容器
     * 
     * @param  string            $abstract
     * @param  \Closure|instance $concrete
     * @return void
     */
    public function bind($abstract, $concrete)
    {
        if ($concrete instanceof \Closure) {
            $this->binds[$abstract] = $concrete;
        } else {
            $this->instances[$abstract] = $concrete;
        }
    }

    /**
     * 解决特定类型的容器
     * 
     * @param  string  $abstract
     * @param  array   $parameters
     * @return mixed
     */
    public function make($abstract, $parameters = [])
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        array_unshift($parameters, $this);

        return call_user_func_array($this->binds[$abstract], $parameters);
    }

    /**
     * 扫描服务层目录获取所有文件目录
     * 
     * @param  string   $path
     * @param  array    $data
     * @return array
     */
    public function scan($path, &$data)
    {
        if (is_dir($path)) {
            $dp = dir($path);
            while ($file = $dp->read()) {
                if ($file != '.' && $file != '..') {
                    $this->scan($path . '/' . $file, $data);
                }
            }
            $dp->close();
        }

        if (is_file($path)) {
            $data[] = $path;
        }
        
        return $data;
    }

    /**
     * 解决抽象转具象（扫描版）
     * 
     * @param  string   $abstract
     * @return instance
     */
    public function resolvedScan($abstract)
    {
        $data = [];
        $path = __DIR__.'/..';
        $this->scan($path, $data);
        $concrete = '';
        foreach ($data as $value) {
            if (basename($value, '.php') === $abstract) {
                $value = str_replace([$path, '.php', '/'], ['\\App\\Services', '', '\\'], $value);
                $concrete = new $value();
            }
        }
        if ($concrete === '') {
            error($abstract . '类不存在');
        }

        return $concrete;
    }

    /**
     * 解决抽象转具象
     * 
     * @param  string   $abstract
     * @return instance
     */
    public function resolved($abstract)
    {
        $abstract = '\\App\\Services\\' . $abstract;

        $concrete = new $abstract();

        return $concrete;
    }

    /**
     * 混合服务容器
     * 
     * @param  array $parameters
     * @return mixed
     */
    public function M($parameters)
    {
        if ( !is_array($parameters) ) {
            $parameters = [$parameters];
        }

        if ( !count($parameters) || in_array('Service', $parameters) ) {
            error('创建失败');
        }

        $this->bind('Service', function($sc, $moduleName) use($parameters) {
            $o = new Service();
            return $o->vendor($sc->make($moduleName));
        });

        foreach ($parameters as $key => $value) {
            $v =  substr(strrchr( $value, '.' ), 1) ?: $value;
            if ($value !== $v) {
                $value = str_replace('.', '\\', $value);
            }

            $this->bind($v, function($sc) use ($value) {
                return $this->resolved($value);
            });
            
            $parameters[$key] = $v;
        }

        return $this->make('Service', $parameters);
    }

    /**
     * 单个指定容器
     * 
     * @param  string $abstract  
     * @return mixed
     */
    public function S($abstract)
    {
        if ( $abstract === 'Service' ) {
            error('容器创建失败');
        }

        $this->bind($abstract, function($sc) use ($abstract) {
            return $this->resolved($abstract);
        });

        return $this->make($abstract);
    }

    /**
     * 纯的服务容器
     * 
     * @return mixed
     */
    public function P()
    {
        return new Service();
    }
}
