# 快速开始

## 配置项目

1. 安装 [Docker](https://docs.docker.com/get-started/) 和 [Docker Compose](https://docs.docker.com/compose/install/)，不用多说

2. 构建 Docker 镜像。目前还没有自动构建，需要手动构建。

```shell
# 克隆项目(输入账密)
git clone https://git.1v.fit/shaobao/NewGPW.git --depth 1

# 构建 Docker 镜像
cd NewGPW && docker build -t gpw-web . && cd docker/mysql && docker build -t gpw-mysql . && cd ../manticore && docker build -t gpw-manticore .
```

构建的镜像会存储在本地镜像仓库中。

3. 编写 docker-compose.yml 文件

```shell
# 创建项目目录并cd
cd && mkdir -p GazellePW && cd GazellePW

# 创建 docker-compose.yml 文件
vi docker-compose.yml
```

输入以下内容

```yaml
services:
  web:
    image: gpw-web
    container_name: gpw-web
    ports:
      - 9000:80
    depends_on:
      -memcached
      -mysql
    volumes:
      - ./php:/config
      - ./cache:/var/www/.cache
      - ./images:/var/www/public/image
      - ./nginx:/etc/nginx/conf.d
    environment:
      - MYSQL_USER=gazelle
      - MYSQL_PASSWORD=password
  memcached:
    image: memcached:1.5-alpine
    container_name: gpw-memcached
  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    container_name: gpw-phpmyadmin
    environment:
      - PMA_HOST=mysql
      - PMA_USER=gazelle
      - PMA_PASSWORD=password
    depends_on:
      - mysql
    ports:
      - 9001:80
  mysql:
    image: gpw-mysql
    container_name: gpw-mysql
    ports:
      - 36000:3306
    volumes:
      - ./mysql:/var/lib/mysql
    environment:
      - MYSQL_DATABASE=gazelle
      - MYSQL_USER=gazelle
      - MYSQL_PASSWORD=password
      - MYSQL_ROOT_PASSWORD=em%G9Lrey4^N

  manticoresearch:
    image: gpw-manticore
    container_name: gpw-manticoresearch
    depends_on:
      - mysql
      - web
    environment:
      # These should match what you set for your mysql container above
      - MYSQL_USER=gazelle
      - MYSQL_PASSWORD=password
```

4. 运行。

```shell
# 启动容器
docker-compose up -d

# 停止容器
docker-compose down
```

5. 现在你可以通过 http://localhost:9000 访问网站。

6. 注册用户: 可以查看 `./cache/emails` 查找本地邮件来激活账号。

7. 如果你需要 Tracker, 部署[Ocelot](https://github.com/Mosasauroidea/Ocelot)。

## 目录说明

```tree
.
├── cache
├── docker-compose.yml
├── images
├── mysql
├── nginx
│   └── gazelle.conf
└── php
    └── config.local.php
```

- `cache` 文件夹：是用来保存缓存文件的
- `images` 文件夹：保存用户上传的图片
- `mysql` 文件夹：数据库文件
- `nginx/gazelle.conf`：nginx配置文件。`nginx` 目录对应至容器中的 `/etc/nginx/conf.d`
- `php/config.local.php`：基础配置文件

这些文件夹保存着用户数据，不可乱动！
