可以按照以下步骤来部署和运行程序:
1.请确保机器@已经安装了Yaf框架, 并且已经加载入PHP;
2.把demo2目录Copy到Webserver的DocumentRoot目录下;
3.需要在php.ini里面启用如下配置，生产的代码才能正确运行：
	yaf.environ="product"
4.重启Webserver;
5.访问http://yourhost/demo2/,出现Hellow Word!, 表示运行成功,否则请查看php错误日志;
13e15c36a8841100e8351a7b998574ae