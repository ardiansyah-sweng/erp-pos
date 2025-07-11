# 🛡️ Branch Protection Setup Guide

## Manual Setup Required (Repository Owner Only)

Since branch protection rules cannot be set via code, you need to configure them manually in GitHub:

### 1. Navigate to Branch Protection Settings
Visit: https://github.com/ardiansyah-sweng/erp-pos/settings/branches

### 2. Protection Rules for `main` Branch

Click **"Add rule"** and configure:

#### Basic Settings:
- **Branch name pattern**: `main`
- ✅ **Restrict pushes that create files over 100MB**

#### Pull Request Settings:
- ✅ **Require a pull request before merging**
- ✅ **Require approvals**: 1 (or more for team)
- ✅ **Dismiss stale PR approvals when new commits are pushed**
- ✅ **Require review from code owners** (if CODEOWNERS file exists)

#### Status Check Settings:
- ✅ **Require status checks to pass before merging**
- ✅ **Require branches to be up to date before merging**
- **Required status checks**: (Will appear after first CI run)
  - `test`
  - `security`

#### Additional Restrictions:
- ✅ **Restrict pushes that create files over 100MB**
- ✅ **Include administrators** (enforces rules for admins too)
- ✅ **Allow force pushes**: ❌ (disabled for safety)
- ✅ **Allow deletions**: ❌ (disabled for safety)

### 3. Protection Rules for `develop` Branch

Create another rule with:

#### Basic Settings:
- **Branch name pattern**: `develop`
- ✅ **Restrict pushes that create files over 100MB**

#### Pull Request Settings:
- ✅ **Require a pull request before merging**
- ✅ **Require approvals**: 1
- ✅ **Dismiss stale PR approvals when new commits are pushed**

#### Status Check Settings:
- ✅ **Require status checks to pass before merging**
- ✅ **Require branches to be up to date before merging**
- **Required status checks**:
  - `test`
  - `security`

## 🔄 Recommended Workflow After Setup

### For Feature Development:
```bash
# 1. Start from develop
git checkout develop
git pull origin develop

# 2. Create feature branch
git checkout -b feature/your-feature-name

# 3. Make changes and commit
git add .
git commit -m "feat: add your feature"

# 4. Push and create PR
git push origin feature/your-feature-name
# Then create PR to develop branch via GitHub UI
```

### For Release to Production:
```bash
# 1. Create PR from develop to main
# 2. After approval and merge, tag release
git checkout main
git pull origin main
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

## 📋 Validation Checklist

After setting up branch protection, verify:

- [ ] Cannot push directly to `main` branch
- [ ] Cannot push directly to `develop` branch  
- [ ] PR required for changes to protected branches
- [ ] CI checks must pass before merge
- [ ] At least 1 approval required for PRs
- [ ] Force push and deletion disabled

## 🚨 Emergency Procedures

In case of critical hotfixes:

1. **Option A**: Temporarily disable branch protection
2. **Option B**: Create hotfix branch and emergency PR
3. **Option C**: Use admin override (if enabled)

**Always re-enable protection after emergency fixes!**

---

**⚠️ Important**: These settings ensure code quality and prevent accidental direct pushes to important branches.
