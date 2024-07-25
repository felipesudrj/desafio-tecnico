<html>

<head>
    <title><?= $this->fetch('title') ?></title>
    <?= $this->Html->css('/css/bootstrap.min.css') ?>
    <?= $this->Html->css('/css/style.css') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>

<body>


    <main class="main">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    

    <?= $this->Html->script('/js/jquery-3.7.1.min'); ?>
    <?= $this->Html->script('/js/bootstrap.min.js'); ?>
    <?= $this->fetch('script') ?>
</html>