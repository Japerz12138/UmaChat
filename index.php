<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>UmaChat</title>
    <link href="./resources/css/stylesheet.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">UmaChat</h1>
    <div class="row">
        <div class="col-lg-4">
            <form id="messageForm">
                <label for="name" class="form-label">名称：</label>
                <input type="text" id="nameSearch" class="form-control mb-3" placeholder="搜索名称">
                <select name="name" id="name" class="form-select mb-3">
                    <!-- 名称选项将通过JavaScript动态生成 -->
                </select>
                <label for="message" class="form-label">留言：</label>
                <textarea name="message" id="message" class="form-control mb-3" rows="4" cols="50"></textarea>
                <button type="submit" class="btn btn-primary">发布</button>
            </form>
        </div>
        <div class="col-lg-8">
            <div id="messages" class="row row-cols-1">
                <!-- 留言内容将会显示在这里 -->
            </div>
            <div id="pagination" class="d-flex justify-content-center align-items-center mt-3 mb-5">
                <!-- 分页控件将会显示在这里 -->
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">回复</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm">
                    <div class="mb-3">
                        <label for="replyNameSearch" class="form-label">名称：</label>
                        <input type="text" id="replyNameSearch" class="form-control mb-3" placeholder="搜索名称">
                        <select name="name" id="replyName" class="form-select mb-3">
                            <!-- 名称选项将通过JavaScript动态生成 -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="replyMessage" class="form-label">回复：</label>
                        <textarea name="message" id="replyMessage" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">发送</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- 引入Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./resources/js/script.js"></script>
</body>
</html>
