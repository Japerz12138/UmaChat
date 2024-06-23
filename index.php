<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>UmaChat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./resources/img/ui/favicon.png" type="image/png"> <!-- 网站图标 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="./resources/css/stylesheet.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <img src="./resources/img/ui/logo.png" alt="UmaChat Logo" class="img-fluid" style="max-width: 100%; height: auto;">
    </div>
    <div class="d-flex justify-content-center mb-4">
        <button type="button" class="btn-image me-2" data-bs-toggle="modal" data-bs-target="#rulesModal">
            <div class="btn-content">
                <img src="./resources/ui/rule_button.png" alt="规则" class="img-fluid">
                <span class="btn-text rules-text">规则</span>
            </div>
        </button>
        <button type="button" class="btn-image" data-bs-toggle="modal" data-bs-target="#aboutModal">
            <div class="btn-content">
                <img src="./resources/ui/about_button.png" alt="关于" class="img-fluid">
                <span class="btn-text about-text">关于</span>
            </div>
        </button>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div id="newMessageCard">
                <div class="card-header new-message-header text-white">发表新留言</div>
                <form id="messageForm">
                    <label for="name" class="form-label">名称：</label>
                    <input type="text" id="nameSearch" class="form-control mb-3" placeholder="搜索名称">
                    <select name="name" id="name" class="form-select mb-3"></select>
                    <label for="message" class="form-label">留言：</label>
                    <textarea name="message" id="message" class="form-control mb-3" rows="4" cols="50"></textarea>
                    <div class="text-end text-muted" id="messageCount">0/100</div>
                    <label for="mood" class="form-label">心情：</label>
                    <select name="mood" id="mood" class="form-select mb-3">
                        <option value="普通">普通</option>
                        <option value="绝不调">绝不调</option>
                        <option value="不调">不调</option>
                        <option value="好调">好调</option>
                        <option value="绝好调">绝好调</option>
                    </select>
                    <button type="submit" id="messageSubmit" class="btn btn-primary d-block mx-auto">发布</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8 col-md-6 col-sm-12">
            <div id="messages" class="row row-cols-1"></div>
            <div id="pagination" class="d-flex justify-content-center align-items-center mt-3 mb-5"></div>
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
                        <select name="name" id="replyName" class="form-select mb-3"></select>
                    </div>
                    <div class="mb-3">
                        <label for="replyMessage" class="form-label">回复：</label>
                        <textarea name="message" id="replyMessage" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary d-block mx-auto">发送</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Live Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">UmaChat系统消息</strong>
            <small id="toastTime">刚刚</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            这只是一个可爱的占位符捏~
        </div>
    </div>
</div>

<!-- Rules Modal -->
<div class="modal fade" id="rulesModal" tabindex="-1" aria-labelledby="rulesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rulesModalLabel">规则</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>1. 角色扮演</h5>
                <ul>
                    <li>用户需选择并扮演赛马娘中的角色，以该角色的口吻发布留言信息。</li>
                    <li>禁止发布与角色设定明显不符的内容（OOC）。</li>
                </ul>
                <h5>2. 禁止不良内容</h5>
                <ul>
                    <li>严禁发布任何形式的辱骂、侮辱或攻击性言论。</li>
                    <li>禁止发布涉及政治、宗教等敏感话题的内容。</li>
                    <li>禁止发布任何形式的淫秽、暴力或其他不良内容。</li>
                </ul>
                <h5>3. 信息管理</h5>
                <ul>
                    <li>管理员有权删除违反规定的留言信息，并对相关用户进行封禁处理。</li>
                    <li>如发现违反规定的内容，请及时向杰帕斯举报。(请包含截图并使留言ID可见)</li>
                </ul>
                <h5>4. 维护良好交流环境</h5>
                <ul>
                    <li>尊重他人，保持友善和礼貌的交流。</li>
                    <li>禁止刷屏和恶意灌水，保持信息流通顺畅。</li>
                </ul>
                <h5>5. 其他规定</h5>
                <ul>
                    <li>请勿发布任何与赛马娘角色扮演无关的广告或宣传信息。</li>
                    <li>用户应对自己发布的内容负责，遵守国家法律法规。</li>
                </ul>
                <p>希望大家遵守以上规则，维护一个良好的交流环境。祝大家在这里愉快交流，享受角色扮演的乐趣！</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<!-- About Modal -->
<div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aboutModalLabel">关于</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>这里是？</h5>
                <p>欢迎来到赛马娘角色扮演匿名聊天板！在这里，您可以选择并扮演赛马娘中的角色，并以该角色的口吻发布留言信息，切身带入你喜欢的马娘的视角，分享美好事物的点点滴滴。我们致力于为大家提供一个有趣、安全和友好的交流平台。</p>
                <h5>管理者</h5>
                <p>杰帕斯(QQ: 1952135253) [VRC马娘吧]</p>
                <h5>UmaChat版本</h5>
                <p>v1.0.0 (Initial Publish) [20240624001]</p>
                <h5>版权说明</h5>
                <p>本站部分资源来源于网络，以及Cygames旗下的游戏“ウマ娘”游戏资源。如果侵权请联系站长对侵权内容进行删除处理。</p>
                <p>请务必支持正版游戏：https://umamusume.jp/</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./resources/js/script.js"></script>
</body>
</html>
