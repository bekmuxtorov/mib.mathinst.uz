from django.contrib import admin
from .models import Article, Issue

# Register your models here.


class IssueAdmin(admin.ModelAdmin):
    list_display = ["id", "name_uz", "file_link", "created_at"]
    search_fields = ("name_uz",)


class ArticleAdmin(admin.ModelAdmin):
    list_display = ["id", "name_uz", "created_at"]


admin.site.register(Article, ArticleAdmin)
admin.site.register(Issue, IssueAdmin)
